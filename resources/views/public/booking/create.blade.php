@extends('layouts.app')

@section('title', 'Form Reservasi Sesi Foto — LensMatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
  .leaflet-container { font-family: inherit; z-index: 1; border-radius: 1rem; }
  .leaflet-control-layers { border-radius: 0.75rem !important; border: 1px solid rgba(0,0,0,0.08) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; font-size: 11px !important; font-weight: 700 !important; }
  .leaflet-bar { border-radius: 0.75rem !important; overflow: hidden; border: 1px solid rgba(0,0,0,0.08) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }
  .leaflet-bar a { border-bottom: 1px solid #f3f4f6 !important; }
  .leaflet-bar a:last-child { border-bottom: none !important; }
</style>
@endpush

@section('content')
<section class="py-10 sm:py-14 lg:py-16 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10" 
       @booking-city-set.window="cityCode = $event.detail.code; touched.city = true; validateCity(); if (!$event.detail.skipPan && $event.detail.lat && $event.detail.lng) { panMapTo($event.detail.lat, $event.detail.lng, 12); }"
       x-data="{
    provincesList: {{ Illuminate\Support\Js::from($provinces->map(fn($p) => ['code' => (string)$p->code, 'name' => $p->name, 'lat' => !empty($p->meta['lat']) ? (float)$p->meta['lat'] : null, 'lng' => !empty($p->meta['long']) ? (float)$p->meta['long'] : null])) }},
    allServices: {{ Illuminate\Support\Js::from($allServices->map(function($s) {
        return [
            'id' => $s->id,
            'nama' => $s->nama_layanan,
            'harga' => (int) $s->tarif_harga,
            'deskripsi' => $s->deskripsi_layanan,
            'spare_time_enabled' => (bool) $s->spare_time_enabled,
            'spare_before' => $s->getEffectiveSpareBeforeMinutes(),
            'spare_after' => $s->getEffectiveSpareAfterMinutes(),
            'spare_summary' => $s->spare_summary,
            'allow_same_location_bypass' => (bool) $s->allow_same_location_bypass,
            'details' => $s->details->map(function($d) {
                return [
                    'id' => $d->id,
                    'nama' => $d->nama_fitur,
                ];
            })->values(),
        ];
    })) }},
    selectedServiceId: {{ $service ? $service->id : 'null' }},
    paymentType: '{{ old('payment_type', 'full') === 'dp' ? 'dp' : 'full' }}',
    selectedDate: '{{ old('tanggal_sesi', $preselectedDate ?? '') }}',
    selectedStartTime: '{{ old('jam_mulai', '') }}',
    selectedEndTime: '{{ old('jam_selesai', '') }}',
    openStartDropdown: false,
    openEndDropdown: false,

    workingHours: {{ Illuminate\Support\Js::from($workingHours ?? []) }},
    blockedDates: {{ Illuminate\Support\Js::from($blockedDates ?? []) }},

    slotLoading: false,
    slotAbortCtrl: null,
    slotMatrix: {
      is_open: true,
      has_available_slots: false,
      reason: null,
      operating_window: null,
      start_options: [],
      end_options_by_start: {}
    },

    // Lokasi & Peta
    provinceCode: '{{ old('province_code', '') }}',
    cityCode: '{{ old('city_code', '') }}',
    lokasi_acara: {{ Illuminate\Support\Js::from(old('lokasi_acara', '')) }},
    latitude: '{{ old('latitude', '') }}',
    longitude: '{{ old('longitude', '') }}',
    mapInstance: null,
    mapMarker: null,
    mapQuery: '',
    mapSearching: false,
    isDetectingLocation: false,
    mapResults: [],
    mapNotice: '',

    // Booking Custom Modal State
    customModalOpen: false,
    customForm: {
      tanggal_mulai: '{{ old('tanggal_mulai', '') }}',
      tanggal_selesai: '{{ old('tanggal_selesai', '') }}',
      jam_mulai: '{{ old('jam_mulai_custom', '') }}',
      jam_selesai: '{{ old('jam_selesai_custom', '') }}',
      lokasi: {{ Illuminate\Support\Js::from(old('lokasi', '')) }},
      kebutuhan: {{ Illuminate\Support\Js::from(old('kebutuhan', '')) }},
    },
    customError: '',

    // Mini Calendar State
    calendarYear: new Date('{{ old('tanggal_sesi', $preselectedDate ?: date('Y-m-d')) }}' + 'T00:00:00').getFullYear(),
    calendarMonth: new Date('{{ old('tanggal_sesi', $preselectedDate ?: date('Y-m-d')) }}' + 'T00:00:00').getMonth(),
    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],

    init() {
      const todayStr = this.getLocalTodayStr();
      if (this.selectedDate) {
        if (this.selectedDate < todayStr || this.isDateFullDayBlocked(this.selectedDate)) {
          this.selectedDate = '';
          this.selectedStartTime = '';
          this.selectedEndTime = '';
        }
      }

      if (this.provinceCode) {
        fetchBookingCities(this.provinceCode, this.cityCode);
      }

      this.$nextTick(() => {
        this.initLeafletMap();
        setTimeout(() => {
          if (this.mapInstance) this.mapInstance.invalidateSize();
        }, 200);
        if (this.selectedDate) {
          this.fetchAvailableSlots(true);
        }
      });
    },

    getLocalTodayStr() {
      const now = new Date();
      return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    },

    initLeafletMap() {
      if (typeof L === 'undefined' || !this.$refs.bookingMap) return;

      const hasInitial = this.latitude !== '' && this.longitude !== '';
      const fallbackLat = {{ !empty($photographer->city?->meta['lat']) ? (float)$photographer->city->meta['lat'] : -6.200000 }};
      const fallbackLng = {{ !empty($photographer->city?->meta['long']) ? (float)$photographer->city->meta['long'] : 106.816666 }};
      const fallbackZoom = {{ !empty($photographer->city?->meta['lat']) ? 12 : 11 }};

      const centerLat = hasInitial ? parseFloat(this.latitude) : fallbackLat;
      const centerLng = hasInitial ? parseFloat(this.longitude) : fallbackLng;
      const zoomLevel = hasInitial ? 15 : fallbackZoom;

      this.mapInstance = L.map(this.$refs.bookingMap).setView([centerLat, centerLng], zoomLevel);

      const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
      });

      const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
      });

      googleRoadmap.addTo(this.mapInstance);

      L.control.layers({
        'Peta Jalan': googleRoadmap,
        'Satelit': googleHybrid
      }, null, { position: 'topright' }).addTo(this.mapInstance);

      if (hasInitial) {
        this.placeMarker(centerLat, centerLng, false);
      } else {
        // Deteksi otomatis lokasi awal perangkat pengguna via Browser Geolocation API
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (pos) => {
              // Jika izin diberikan dan pengguna belum menandai titik pin khusus
              if (!this.latitude && !this.longitude && !this.provinceCode && this.mapInstance) {
                this.panMapTo(pos.coords.latitude, pos.coords.longitude, 13);
              }
            },
            () => {
              // Izin ditolak: peta tetap tenang pada fallback domisili kota fotografer
            },
            { enableHighAccuracy: true, timeout: 8000 }
          );
        }
      }

      this.mapInstance.on('click', (e) => {
        this.placeMarker(e.latlng.lat, e.latlng.lng, true);
        this.reverseGeocodeAndMatch(e.latlng.lat, e.latlng.lng);
      });
    },

    panMapTo(lat, lng, zoom = 12) {
      if (!this.mapInstance || lat === '' || lng === '' || lat === null || lng === null) return;
      const targetLat = parseFloat(lat);
      const targetLng = parseFloat(lng);
      if (isNaN(targetLat) || isNaN(targetLng)) return;
      this.mapInstance.flyTo([targetLat, targetLng], zoom, { duration: 1.0 });
    },

    placeMarker(lat, lng, refreshSlots = true) {
      const fixedLat = Number(lat).toFixed(6);
      const fixedLng = Number(lng).toFixed(6);
      this.latitude = fixedLat;
      this.longitude = fixedLng;
      this.mapNotice = '';

      if (this.mapInstance) {
        if (this.mapMarker) {
          this.mapMarker.setLatLng([lat, lng]);
        } else {
          this.mapMarker = L.marker([lat, lng], { draggable: true }).addTo(this.mapInstance);
          this.mapMarker.on('dragend', () => {
            const pos = this.mapMarker.getLatLng();
            this.latitude = Number(pos.lat).toFixed(6);
            this.longitude = Number(pos.lng).toFixed(6);
            if (this.selectedDate) {
              this.fetchAvailableSlots(false);
            }
            this.reverseGeocodeAndMatch(pos.lat, pos.lng);
          });
        }
        this.mapInstance.setView([lat, lng], Math.max(this.mapInstance.getZoom(), 15));
      }

      if (refreshSlots && this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
    },

    clearMarker() {
      this.latitude = '';
      this.longitude = '';
      this.mapQuery = '';
      this.mapResults = [];
      this.mapNotice = '';
      if (this.mapMarker && this.mapInstance) {
        this.mapInstance.removeLayer(this.mapMarker);
        this.mapMarker = null;
      }
      if (this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
    },

    normalizeGeoName(str) {
      if (!str) return '';
      return String(str).toLowerCase()
        .replace(/^provinsi\s+/i, '')
        .replace(/^daerah\s+khusus\s+ibukota\s+/i, '')
        .replace(/^daerah\s+istimewa\s+/i, '')
        .replace(/^dki\s+/i, '')
        .replace(/^di\s+/i, '')
        .replace(/^kabupaten\s+/i, '')
        .replace(/^kab\.\s+/i, '')
        .replace(/^kota\s+/i, '')
        .replace(/[^a-z0-9]/g, '')
        .trim();
    },

    findMatchingProvince(rawState) {
      if (!rawState) return null;
      const clean = this.normalizeGeoName(rawState);
      if (!clean) return null;

      let found = this.provincesList.find(p => this.normalizeGeoName(p.name) === clean);
      if (found) return found;

      found = this.provincesList.find(p => {
        const pNorm = this.normalizeGeoName(p.name);
        return pNorm.includes(clean) || clean.includes(pNorm);
      });
      if (found) return found;

      const aliasMap = {
        'jakarta': '31',
        'dkijakarta': '31',
        'yogyakarta': '34',
        'diyogyakarta': '34',
        'jogja': '34',
        'westjava': '32',
        'centraljava': '33',
        'eastjava': '35',
        'westnusatenggara': '52',
        'eastnusatenggara': '53',
        'northsumatra': '12',
        'westsumatra': '13',
        'southsumatra': '16',
        'bangkabelitung': '19',
        'riauislands': '21',
        'westkalimantan': '61',
        'centralkalimantan': '62',
        'southkalimantan': '63',
        'eastkalimantan': '64',
        'northkalimantan': '65',
        'northsulawesi': '71',
        'centralsulawesi': '72',
        'southsulawesi': '73',
        'southeastsulawesi': '74',
        'westsulawesi': '76',
        'northmaluku': '82',
        'westpapua': '92'
      };
      if (aliasMap[clean]) {
        return this.provincesList.find(p => String(p.code) === String(aliasMap[clean])) || null;
      }
      return null;
    },

    async reverseGeocodeAndMatch(lat, lng, preferredState = '', preferredCity = '') {
      let stateCandidate = preferredState;
      let cityCandidate = preferredCity;
      let venueTitle = '';
      let fullAddress = '';

      if (!stateCandidate || !cityCandidate) {
        try {
          const photonUrl = `https://photon.komoot.io/reverse?lat=${lat}&lon=${lng}`;
          const res = await fetch(photonUrl);
          const data = await res.json();
          if (data && data.features && data.features.length > 0) {
            const props = data.features[0].properties || {};
            stateCandidate = stateCandidate || props.state || '';
            cityCandidate = cityCandidate || props.county || props.city || props.district || '';
            venueTitle = props.name || props.street || '';
            const parts = [];
            if (props.street) parts.push(props.street + (props.housenumber ? ' No. ' + props.housenumber : ''));
            if (props.district) parts.push(props.district);
            if (props.city || props.county) parts.push(props.city || props.county);
            if (props.state) parts.push(props.state);
            fullAddress = (venueTitle ? venueTitle + ', ' : '') + parts.join(', ');
          }
        } catch (e) {
          // Fallback Nominatim jika Photon gagal
        }

        if (!stateCandidate || !cityCandidate) {
          try {
            const nomUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14&addressdetails=1`;
            const nomRes = await fetch(nomUrl, { headers: { 'Accept': 'application/json' } });
            const nomData = await nomRes.json();
            if (nomData && nomData.address) {
              stateCandidate = stateCandidate || nomData.address.state || '';
              cityCandidate = cityCandidate || nomData.address.city || nomData.address.town || nomData.address.county || nomData.address.city_district || '';
              if (!fullAddress) fullAddress = nomData.display_name || '';
              if (!venueTitle) venueTitle = nomData.name || nomData.address?.hotel || nomData.address?.building || nomData.address?.tourism || nomData.address?.amenity || nomData.address?.road || '';
            }
          } catch (e) {
            // Error ditangani secara senyap
          }
        }
      }

      const detectedQuery = venueTitle || (fullAddress ? fullAddress.split(',')[0].trim() : '');
      if (detectedQuery) {
        this.mapQuery = detectedQuery;
      }

      if (fullAddress) {
        if (!this.lokasi_acara || !this.lokasi_acara.trim() || this.isDetectingLocation) {
          this.lokasi_acara = fullAddress;
          this.touched.lokasi = true;
          this.validateLokasi();
        }
      }

      if (stateCandidate) {
        const matchedProv = this.findMatchingProvince(stateCandidate);
        if (matchedProv) {
          this.provinceCode = matchedProv.code;
          this.touched.province = true;
          this.validateProvince();
          window.dispatchEvent(new CustomEvent('booking-province-set', {
            detail: { code: matchedProv.code, name: matchedProv.name }
          }));

          fetchBookingCities(matchedProv.code, '', cityCandidate);
          this.mapNotice = `Lokasi terdeteksi: ${matchedProv.name}${cityCandidate ? ' — ' + cityCandidate : ''}`;
          return;
        }
      }

      this.mapNotice = `Titik koordinat terpilih: ${lat}, ${lng}`;
    },

    formatPhotonResult(feat) {
      const p = feat.properties || {};
      const coords = feat.geometry?.coordinates || [0, 0];
      const lng = Number(coords[0]).toFixed(6);
      const lat = Number(coords[1]).toFixed(6);
      const title = p.name || p.street || p.city || 'Titik Lokasi';
      
      const subParts = [];
      if (p.street && p.street !== title) subParts.push(p.street + (p.housenumber ? ' No. ' + p.housenumber : ''));
      if (p.district) subParts.push(p.district);
      if (p.city || p.county) subParts.push(p.city || p.county);
      if (p.state) subParts.push(p.state);

      let tag = '';
      const osmVal = p.osm_value || '';
      if (['hotel', 'guest_house', 'motel'].includes(osmVal)) tag = 'Hotel';
      else if (['beach'].includes(osmVal)) tag = 'Pantai';
      else if (['restaurant', 'cafe', 'bar', 'pub', 'food_court'].includes(osmVal)) tag = 'Kuliner';
      else if (['tourism', 'attraction', 'viewpoint'].includes(osmVal)) tag = 'Wisata';
      else if (['suburb', 'town', 'village', 'city', 'district'].includes(osmVal)) tag = 'Wilayah';

      return {
        lat: parseFloat(lat),
        lon: parseFloat(lng),
        main_title: title,
        sub_title: subParts.join(', ') || p.country || '',
        tag: tag,
        raw_props: p
      };
    },

    formatSearchResult(item) {
      let mainTitle = '';
      let subTitle = '';
      if (item.address) {
        mainTitle = item.address.hotel || item.address.amenity || item.address.building || item.address.tourism || item.address.leisure || item.address.road || item.name || '';
        const parts = [];
        if (item.address.suburb) parts.push(item.address.suburb);
        if (item.address.city || item.address.town || item.address.county) parts.push(item.address.city || item.address.town || item.address.county);
        if (item.address.state) parts.push(item.address.state);
        subTitle = parts.join(', ');
      }
      if (!mainTitle) {
        const chunks = (item.display_name || '').split(',');
        mainTitle = chunks[0] ? chunks[0].trim() : '';
        subTitle = chunks.slice(1, 4).join(', ').trim();
      }
      return {
        lat: parseFloat(item.lat),
        lon: parseFloat(item.lon),
        main_title: mainTitle,
        sub_title: subTitle || item.display_name,
        tag: 'Peta',
        raw_props: {
          state: item.address?.state || '',
          city: item.address?.city || item.address?.county || item.address?.town || ''
        }
      };
    },

    async searchMapLocation(isLive = false) {
      const q = (this.mapQuery || '').trim();
      if (q.length < 3) {
        this.mapResults = [];
        if (!isLive) this.mapNotice = 'Masukkan minimal 3 huruf untuk mencari lokasi di peta.';
        return;
      }
      this.mapSearching = true;
      this.mapNotice = '';

      try {
        let lat = -6.2000;
        let lon = 106.8166;
        if (this.mapInstance) {
          const center = this.mapInstance.getCenter();
          lat = center.lat.toFixed(5);
          lon = center.lng.toFixed(5);
        }

        // 1. Prioritas Utama: Photon Komoot (Elasticsearch OSM, Fuzzy match + typo tolerance + location bias)
        const photonUrl = `https://photon.komoot.io/api/?q=${encodeURIComponent(q)}&lat=${lat}&lon=${lon}&limit=8`;
        const res = await fetch(photonUrl);
        const data = await res.json();

        if (data && data.features && data.features.length > 0) {
          let features = data.features.filter(f => {
            const cc = f.properties?.countrycode || '';
            const c = f.properties?.country || '';
            return cc === 'ID' || c.toLowerCase().includes('indonesia');
          });
          if (features.length === 0) {
            features = data.features.slice(0, 6);
          }
          this.mapResults = features.map(f => this.formatPhotonResult(f));
          return;
        }

        // 2. Fallback Nominatim jika Photon kosong
        let viewboxParam = '';
        if (this.mapInstance) {
          const center = this.mapInstance.getCenter();
          const minLon = (center.lng - 0.6).toFixed(4);
          const maxLat = (center.lat + 0.6).toFixed(4);
          const maxLon = (center.lng + 0.6).toFixed(4);
          const minLat = (center.lat - 0.6).toFixed(4);
          viewboxParam = `&viewbox=${minLon},${maxLat},${maxLon},${minLat}&bounded=0`;
        }
        const nomUrl = 'https://nominatim.openstreetmap.org/search?format=json&countrycodes=id&limit=6&addressdetails=1' + viewboxParam + '&q=' + encodeURIComponent(q);
        const nomRes = await fetch(nomUrl, { headers: { 'Accept': 'application/json' } });
        const nomData = await nomRes.json();
        if (Array.isArray(nomData) && nomData.length > 0) {
          this.mapResults = nomData.map(item => this.formatSearchResult(item));
        } else {
          this.mapResults = [];
          if (!isLive) {
            this.mapNotice = 'Lokasi tidak ditemukan. Coba ketik nama jalan, pantai, atau area sekitar.';
          }
        }
      } catch (err) {
        this.mapResults = [];
        if (!isLive) this.mapNotice = 'Gagal mencari lokasi. Anda tetap dapat menandai titik langsung di peta.';
      } finally {
        this.mapSearching = false;
      }
    },

    pickSearchResult(item) {
      const lat = parseFloat(item.lat);
      const lng = parseFloat(item.lon);
      this.mapQuery = item.main_title || item.sub_title;
      this.mapResults = [];
      this.placeMarker(lat, lng, true);

      const fullAddr = (item.main_title ? item.main_title + ', ' : '') + (item.sub_title || '');
      if (!this.lokasi_acara || !this.lokasi_acara.trim()) {
        this.lokasi_acara = fullAddr;
        this.touched.lokasi = true;
        this.validateLokasi();
      }

      const state = item.raw_props?.state || '';
      const city = item.raw_props?.county || item.raw_props?.city || item.raw_props?.district || '';
      this.reverseGeocodeAndMatch(lat, lng, state, city);
    },

    useCurrentLocation() {
      if (!navigator.geolocation) {
        this.mapNotice = 'Perangkat tidak mendukung deteksi lokasi otomatis.';
        return;
      }
      this.isDetectingLocation = true;
      this.mapNotice = 'Mendeteksi titik lokasi perangkat...';
      navigator.geolocation.getCurrentPosition(
        async (pos) => {
          try {
            this.placeMarker(pos.coords.latitude, pos.coords.longitude, true);
            await this.reverseGeocodeAndMatch(pos.coords.latitude, pos.coords.longitude);
          } catch (e) {
            this.mapNotice = 'Gagal memproses titik lokasi.';
          } finally {
            this.isDetectingLocation = false;
          }
        },
        () => {
          this.mapNotice = 'Izin lokasi ditolak atau tidak tersedia.';
          this.isDetectingLocation = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
      );
    },

    prevMonth() {
      if (this.calendarMonth === 0) {
        this.calendarMonth = 11;
        this.calendarYear--;
      } else {
        this.calendarMonth--;
      }
    },

    nextMonth() {
      if (this.calendarMonth === 11) {
        this.calendarMonth = 0;
        this.calendarYear++;
      } else {
        this.calendarMonth++;
      }
    },

    isDateFullDayBlocked(dateStr) {
      if (!dateStr) return false;
      if (Array.isArray(this.blockedDates) && this.blockedDates.includes(dateStr)) {
        return true;
      }
      const d = new Date(dateStr + 'T00:00:00');
      let dow = d.getDay();
      if (dow === 0) dow = 7;
      const wh = this.workingHours[dow];
      if (wh && !wh.is_open) return true;

      const todayStr = this.getLocalTodayStr();
      if (dateStr === todayStr && wh && !wh.is_24_hours && wh.jam_tutup) {
        const now = new Date();
        const currentMin = (now.getHours() * 60) + now.getMinutes();
        const parts = wh.jam_tutup.split(':');
        const closeMin = (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
        if ((currentMin + 120) > closeMin) {
          return true;
        }
      }

      return false;
    },

    get calendarDays() {
      const year = this.calendarYear;
      const month = this.calendarMonth;
      const firstDayIndex = new Date(year, month, 1).getDay();
      const totalDays = new Date(year, month + 1, 0).getDate();
      const prevTotalDays = new Date(year, month, 0).getDate();
      const todayStr = this.getLocalTodayStr();
      const days = [];

      for (let i = firstDayIndex - 1; i >= 0; i--) {
        const dNum = prevTotalDays - i;
        const m = month === 0 ? 12 : month;
        const y = month === 0 ? year - 1 : year;
        const dateStr = `${y}-${String(m).padStart(2, '0')}-${String(dNum).padStart(2, '0')}`;
        days.push({
          dayNumber: dNum,
          dateStr: dateStr,
          isCurrentMonth: false,
          isPast: true,
          isToday: false,
          isFullDayBlocked: false
        });
      }

      for (let i = 1; i <= totalDays; i++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
          dayNumber: i,
          dateStr: dateStr,
          isCurrentMonth: true,
          isPast: dateStr < todayStr,
          isToday: dateStr === todayStr,
          isFullDayBlocked: this.isDateFullDayBlocked(dateStr)
        });
      }

      const remaining = (7 - (days.length % 7)) % 7;
      for (let i = 1; i <= remaining; i++) {
        const m = month === 11 ? 1 : month + 2;
        const y = month === 11 ? year + 1 : year;
        const dateStr = `${y}-${String(m).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
        days.push({
          dayNumber: i,
          dateStr: dateStr,
          isCurrentMonth: false,
          isPast: false,
          isToday: false,
          isFullDayBlocked: false
        });
      }

      return days;
    },

    selectDay(day) {
      if (!this.step2Unlocked || !day.isCurrentMonth || day.isPast || day.isFullDayBlocked) return;
      this.selectedDate = day.dateStr;
      this.touched.date = true;
      this.validateDate();
      this.openStartDropdown = false;
      this.openEndDropdown = false;
      this.fetchAvailableSlots(false);

      const targetRef = (this.provinceCode && this.cityCode && this.lokasi_acara && this.lokasi_acara.trim().length >= 5)
        ? this.$refs.timeSection
        : this.$refs.locationSection;

      this.$nextTick(() => {
        targetRef?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => {
          if (this.mapInstance) this.mapInstance.invalidateSize();
        }, 250);
      });
    },

    async fetchAvailableSlots(preserveSelection = false) {
      if (!this.selectedDate) return;

      if (this.slotAbortCtrl) {
        this.slotAbortCtrl.abort();
      }
      this.slotAbortCtrl = new AbortController();
      this.slotLoading = true;

      try {
        const params = new URLSearchParams({
          photographer_id: '{{ $photographer->id }}',
          date: this.selectedDate
        });
        if (this.selectedServiceId) params.set('service_id', this.selectedServiceId);
        if (this.latitude !== '' && this.longitude !== '') {
          params.set('latitude', this.latitude);
          params.set('longitude', this.longitude);
        }

        const res = await fetch('{{ route('booking.available-slots') }}?' + params.toString(), {
          signal: this.slotAbortCtrl.signal,
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();
        this.slotMatrix = data;

        const stillValidStart = (data.start_options || []).find(
          opt => opt.value === this.selectedStartTime && opt.available
        );

        if (!stillValidStart) {
          this.selectedStartTime = '';
          this.selectedEndTime = '';
        } else {
          const validEnds = (data.end_options_by_start && data.end_options_by_start[this.selectedStartTime]) || [];
          const stillValidEnd = validEnds.find(e => e.value === this.selectedEndTime);
          if (!stillValidEnd) {
            this.selectedEndTime = '';
          }
        }
      } catch (err) {
        if (err.name !== 'AbortError') {
          // Abaikan gangguan sementara jaringan
        }
      } finally {
        this.slotLoading = false;
      }
    },

    get startOptions() {
      const allOpts = this.slotMatrix.start_options || [];
      return allOpts.filter(opt => opt.available);
    },

    get validEndOptions() {
      if (!this.selectedStartTime || !this.slotMatrix.end_options_by_start) return [];
      return this.slotMatrix.end_options_by_start[this.selectedStartTime] || [];
    },

    pickStartTime(opt) {
      if (!this.step4Unlocked || !opt.available) return;
      this.selectedStartTime = opt.value;
      this.openStartDropdown = false;

      const ends = this.validEndOptions;
      if (ends.length > 0) {
        const currentStillValid = ends.find(e => e.value === this.selectedEndTime);
        if (!currentStillValid) {
          this.selectedEndTime = ends[0].value;
        }
      } else {
        this.selectedEndTime = '';
      }

      this.touched.time = true;
      this.validateTime();
    },

    pickEndTime(opt) {
      if (!this.step4Unlocked) return;
      this.selectedEndTime = opt.value;
      this.openEndDropdown = false;
      this.touched.time = true;
      this.validateTime();
      this.$nextTick(() => {
        this.$refs.noteSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    },

    get selectedDurationHours() {
      if (!this.selectedStartTime || !this.selectedEndTime) return 0;
      const sh = parseInt(this.selectedStartTime.split(':')[0], 10);
      const eh = parseInt(this.selectedEndTime.split(':')[0], 10);
      return eh > sh ? (eh - sh) : 0;
    },

    formatDateIndo(dateStr) {
      if (!dateStr) return '-';
      const parts = dateStr.split('-');
      if (parts.length !== 3) return dateStr;
      const y = parts[0];
      const mIdx = parseInt(parts[1], 10) - 1;
      const d = parseInt(parts[2], 10);
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      const dt = new Date(dateStr + 'T00:00:00');
      const dayName = days[dt.getDay()] || '';
      return `${dayName}, ${d} ${months[mIdx] || ''} ${y}`;
    },

    get selectedService() {
      if (!this.selectedServiceId) return null;
      return this.allServices.find(s => s.id === this.selectedServiceId) || null;
    },
    get hourlyRate() {
      return this.selectedService ? this.selectedService.harga : 0;
    },
    get totalBill() {
      if (!this.selectedService) return 0;
      const dur = this.selectedDurationHours > 0 ? this.selectedDurationHours : 1;
      return this.hourlyRate * dur;
    },
    get dpAmount() {
      return Math.round(this.totalBill * 0.3);
    },
    get remainingAmount() {
      return this.totalBill - this.dpAmount;
    },
    get currentPayAmount() {
      return this.paymentType === 'dp' ? this.dpAmount : this.totalBill;
    },

    get step1Done() {
      return Boolean(this.selectedServiceId);
    },
    get step2Unlocked() {
      return this.step1Done;
    },
    get step2Done() {
      return this.step2Unlocked && Boolean(this.selectedDate);
    },
    get step3Unlocked() {
      return this.step2Done;
    },
    get step3Done() {
      return this.step3Unlocked && Boolean(this.provinceCode) && Boolean(this.cityCode) && Boolean(this.lokasi_acara && this.lokasi_acara.trim().length >= 5);
    },
    get step4Unlocked() {
      return this.step3Done;
    },
    get step4Done() {
      return this.step4Unlocked && Boolean(this.selectedStartTime) && Boolean(this.selectedEndTime) && this.selectedDurationHours >= 1;
    },
    get step5Unlocked() {
      return this.step4Done;
    },
    get isFormReady() {
      return this.step4Done;
    },

    touched: {
      service: {{ $errors->has('service_id') ? 'true' : 'false' }},
      date: {{ $errors->has('tanggal_sesi') ? 'true' : 'false' }},
      time: {{ ($errors->has('jam_mulai') || $errors->has('jam_selesai')) ? 'true' : 'false' }},
      province: {{ $errors->has('province_code') ? 'true' : 'false' }},
      city: {{ $errors->has('city_code') ? 'true' : 'false' }},
      lokasi: {{ $errors->has('lokasi_acara') ? 'true' : 'false' }},
    },
    errors: {
      service: '{{ $errors->first('service_id') ?: '' }}',
      date: '{{ $errors->first('tanggal_sesi') ?: '' }}',
      time: '{{ $errors->first('jam_mulai') ?: ($errors->first('jam_selesai') ?: '') }}',
      province: '{{ $errors->first('province_code') ?: '' }}',
      city: '{{ $errors->first('city_code') ?: '' }}',
      lokasi: '{{ $errors->first('lokasi_acara') ?: '' }}',
    },

    validateService() {
      if (!this.selectedServiceId) {
        this.errors.service = 'Pilih salah satu paket layanan pemotretan.';
        return false;
      }
      this.errors.service = '';
      return true;
    },
    validateDate() {
      if (!this.selectedDate) {
        this.errors.date = 'Pilih tanggal pelaksanaan sesi foto.';
        return false;
      }
      this.errors.date = '';
      return true;
    },
    validateTime() {
      if (!this.selectedStartTime || !this.selectedEndTime || this.selectedDurationHours < 1) {
        this.errors.time = 'Pilih jam mulai dan selesai sesi pemotretan.';
        return false;
      }
      this.errors.time = '';
      return true;
    },
    validateProvince() {
      if (!this.provinceCode) {
        this.errors.province = 'Pilih provinsi lokasi acara.';
        return false;
      }
      this.errors.province = '';
      return true;
    },
    validateCity() {
      if (!this.cityCode) {
        this.errors.city = 'Pilih kota atau kabupaten lokasi acara.';
        return false;
      }
      this.errors.city = '';
      return true;
    },
    validateLokasi() {
      if (!this.lokasi_acara || !this.lokasi_acara.trim()) {
        this.errors.lokasi = 'Alamat lengkap lokasi acara wajib diisi.';
        return false;
      }
      if (this.lokasi_acara.trim().length < 5) {
        this.errors.lokasi = 'Alamat lengkap minimal 5 karakter.';
        return false;
      }
      this.errors.lokasi = '';
      return true;
    },

    submitBookingForm(e) {
      this.touched.service = true;
      this.touched.date = true;
      this.touched.province = true;
      this.touched.city = true;
      this.touched.lokasi = true;
      this.touched.time = true;

      const sOk = this.validateService();
      const dOk = this.validateDate();
      const pOk = this.validateProvince();
      const cOk = this.validateCity();
      const lOk = this.validateLokasi();
      const tOk = this.validateTime();

      if (!sOk || !dOk || !pOk || !cOk || !lOk || !tOk) {
        e.preventDefault();
        let target = null;
        if (!sOk) target = this.$refs.serviceSection;
        else if (!dOk) target = this.$refs.dateSection;
        else if (!pOk) target = document.getElementById('bookingProvinceBtn') || this.$refs.locationSection || document.getElementById('sectionLocation');
        else if (!cOk) target = document.getElementById('bookingCityBtn') || this.$refs.locationSection || document.getElementById('sectionLocation');
        else if (!lOk) target = this.$refs.lokasiInput || this.$refs.locationSection || document.getElementById('sectionLocation');
        else if (!tOk) target = this.$refs.timeSection;

        if (target) {
          target.focus?.();
          target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
      return true;
    },

    selectService(id) {
      const isNewSelection = (this.selectedServiceId !== id);
      this.selectedServiceId = (this.selectedServiceId === id ? null : id);
      this.touched.service = true;
      this.validateService();
      if (this.selectedDate) {
        this.fetchAvailableSlots(false);
      }
      if (isNewSelection && this.selectedServiceId) {
        this.$nextTick(() => {
          this.$refs.dateSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      }
    },

    openCustomModal() {
      this.customError = '';
      if (!this.customForm.tanggal_mulai && this.selectedDate) {
        this.customForm.tanggal_mulai = this.selectedDate;
      }
      if (!this.customForm.lokasi && this.lokasi_acara) {
        this.customForm.lokasi = this.lokasi_acara;
      }
      this.customModalOpen = true;
    },

    submitCustomRequest(e) {
      this.customError = '';
      if (!this.customForm.tanggal_mulai || !this.customForm.tanggal_selesai) {
        e.preventDefault();
        this.customError = 'Tanggal mulai dan tanggal selesai wajib diisi.';
        return false;
      }
      if (this.customForm.tanggal_selesai < this.customForm.tanggal_mulai) {
        e.preventDefault();
        this.customError = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        return false;
      }
      if (
        this.customForm.tanggal_mulai === this.customForm.tanggal_selesai &&
        this.customForm.jam_mulai &&
        this.customForm.jam_selesai &&
        this.customForm.jam_selesai <= this.customForm.jam_mulai
      ) {
        e.preventDefault();
        this.customError = 'Jam selesai harus lebih akhir daripada jam mulai.';
        return false;
      }
      if (!this.customForm.lokasi || !this.customForm.lokasi.trim()) {
        e.preventDefault();
        this.customError = 'Lokasi acara wajib diisi.';
        return false;
      }
      if (!this.customForm.kebutuhan || !this.customForm.kebutuhan.trim()) {
        e.preventDefault();
        this.customError = 'Rincian kebutuhan acara wajib diisi.';
        return false;
      }
      return true;
    }
  }">

    <!-- Page Header -->
    <div class="text-center space-y-2 max-w-2xl mx-auto">
      <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">
        Form Reservasi Sesi Foto
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
        Pilih paket per jam, tanggal, lokasi acara, lalu tentukan rentang jam mulai dan jam selesai sesi pemotretan Anda.
      </p>
    </div>

    <!-- Booking Form Container (2-Column Grid) -->
    <form action="{{ route('booking.store') }}" method="POST" @submit="submitBookingForm($event)" novalidate class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
      @csrf
      <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">
      <input type="hidden" name="service_id" :value="selectedServiceId || ''">
      <input type="hidden" name="tanggal_sesi" :value="selectedDate">
      <input type="hidden" name="jam_mulai" :value="selectedStartTime">
      <input type="hidden" name="jam_selesai" :value="selectedEndTime">
      <input type="hidden" name="latitude" :value="latitude">
      <input type="hidden" name="longitude" :value="longitude">

      <!-- Kolom Kiri (7 Kolom): Urutan Paket -> Tanggal -> Lokasi -> Jam -> Catatan -->
      <div class="lg:col-span-7 space-y-6 lg:space-y-7">

        <!-- 1. Pilih Paket Layanan (/ jam) -->
        <div x-ref="serviceSection" tabindex="-1" class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">1. Pilih Paket Layanan</h2>
            </div>
          </div>

          <div class="space-y-3">
            @forelse($allServices as $pkg)
              <div @click="selectService({{ $pkg->id }})"
                   class="p-4 sm:p-5 rounded-2xl border transition cursor-pointer select-none space-y-3"
                   :class="selectedServiceId === {{ $pkg->id }}
                            ? 'border-amber-400 bg-amber-50/40 dark:bg-amber-950/20 ring-2 ring-amber-400/40 shadow-xs'
                            : 'border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800/80 hover:border-gray-300 dark:hover:border-gray-600'">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5"
                         :class="selectedServiceId === {{ $pkg->id }} ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                      <span class="w-2 h-2 rounded-full bg-amber-500" x-show="selectedServiceId === {{ $pkg->id }}"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white leading-snug">{{ $pkg->nama_layanan }}</h3>
                      <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mt-0.5 line-clamp-2">{{ $pkg->deskripsi_layanan }}</p>
                      @if($pkg->spare_summary)
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5">
                          Jeda persiapan: {{ $pkg->spare_summary }}{{ $pkg->allow_same_location_bypass ? ' · Bebas jeda untuk lokasi sama' : '' }}
                        </p>
                      @endif
                    </div>
                  </div>
                  <div class="text-right shrink-0">
                    <span class="text-xs sm:text-sm font-black text-amber-600 dark:text-amber-400">Rp {{ number_format($pkg->tarif_harga, 0, ',', '.') }}</span>
                    <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-500">/ jam</span>
                  </div>
                </div>

                @if($pkg->details->count() > 0)
                  <div class="pt-1 pl-7 grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px] text-gray-600 dark:text-gray-300">
                    @foreach($pkg->details->take(4) as $det)
                      <div class="flex items-center gap-1.5 min-w-0">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span class="truncate">{{ $det->nama_fitur }}</span>
                      </div>
                    @endforeach
                    @if($pkg->details->count() > 4)
                      <span class="text-[10px] text-gray-400 font-bold self-center">+{{ $pkg->details->count() - 4 }} benefit lainnya</span>
                    @endif
                  </div>
                @endif
              </div>
            @empty
              <div class="p-6 text-center text-xs text-gray-500 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                Fotografer belum menambahkan paket layanan pemotretan.
              </div>
            @endforelse
          </div>
          <p x-show="touched.service && errors.service" x-text="errors.service" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

          <!-- Opsi Booking Custom Multi-Hari / Jam Khusus -->
          <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200/80 dark:border-gray-700/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="space-y-0.5">
              <p class="text-xs font-bold text-gray-900 dark:text-white">Acara Lebih dari 1 Hari atau Jam Khusus</p>
              <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                Untuk pemotretan berurutan beberapa hari atau di luar jam reguler, ajukan lewat obrolan dengan fotografer.
              </p>
            </div>
            <button type="button"
                    @click="openCustomModal()"
                    class="px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition shrink-0 cursor-pointer">
              Booking Custom
            </button>
          </div>
        </div>

        <!-- 2. Pilih Tanggal Sesi Foto -->
        <div x-ref="dateSection"
             tabindex="-1"
             :class="{ 'opacity-50 pointer-events-none select-none': !step2Unlocked }"
             class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none transition-opacity duration-200">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                   :class="step2Unlocked ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500'">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black transition-colors"
                  :class="step2Unlocked ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">2. Pilih Tanggal Sesi</h2>
            </div>
            <span x-show="!step2Unlocked" class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Pilih Paket di Langkah 1</span>
            <span x-show="step2Unlocked && selectedDate" x-cloak class="text-xs font-bold text-gray-700 dark:text-gray-300" x-text="formatDateIndo(selectedDate)"></span>
          </div>

          <div class="p-4 sm:p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/80 dark:border-gray-700/60 space-y-3.5">
            <div class="flex items-center justify-between">
              <h3 class="text-xs sm:text-sm font-black text-gray-900 dark:text-white tracking-tight"
                  x-text="monthNames[calendarMonth] + ' ' + calendarYear">
              </h3>
              <div class="flex items-center gap-1">
                <button type="button"
                        @click="prevMonth()"
                        :disabled="!step2Unlocked"
                        class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                <button type="button"
                        @click="nextMonth()"
                        :disabled="!step2Unlocked"
                        class="w-7 h-7 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-800 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-black uppercase tracking-wider text-gray-400 dark:text-gray-500">
              <template x-for="dname in dayNames" :key="dname">
                <div class="py-0.5" x-text="dname"></div>
              </template>
            </div>

            <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
              <template x-for="(day, idx) in calendarDays" :key="idx">
                <div @click="selectDay(day)"
                     class="min-h-[46px] p-1.5 rounded-xl border text-center transition flex flex-col items-center justify-center select-none"
                     :class="{
                       'opacity-20 border-transparent cursor-default': !day.isCurrentMonth,
                       'bg-gray-100/60 dark:bg-gray-900/60 border-gray-200/60 dark:border-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed': day.isCurrentMonth && day.isPast,
                       'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200/50 dark:border-rose-900/40 text-rose-500 cursor-not-allowed': day.isCurrentMonth && !day.isPast && day.isFullDayBlocked,
                       'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 hover:border-amber-400 cursor-pointer shadow-2xs': day.isCurrentMonth && !day.isPast && !day.isFullDayBlocked && selectedDate !== day.dateStr,
                       'border-amber-400 bg-amber-400 text-gray-950 font-black shadow-xs ring-2 ring-amber-400/30': day.isCurrentMonth && !day.isPast && !day.isFullDayBlocked && selectedDate === day.dateStr
                     }">
                  <span class="text-xs font-black leading-none" x-text="day.dayNumber"></span>
                  <template x-if="day.isCurrentMonth && day.isToday && !day.isFullDayBlocked && selectedDate !== day.dateStr">
                    <span class="text-[9px] font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">Hari Ini</span>
                  </template>
                  <template x-if="day.isCurrentMonth && !day.isPast && day.isFullDayBlocked">
                    <span class="text-[9px] font-bold text-rose-500 dark:text-rose-400 mt-0.5">Tutup</span>
                  </template>
                </div>
              </template>
            </div>
          </div>
          <p x-show="touched.date && errors.date" x-text="errors.date" x-cloak class="text-[11px] font-bold text-red-500"></p>
        </div>

        <!-- 3. Lokasi Acara (Wilayah + Peta Pin Opsional + Alamat) -->
        <div x-ref="locationSection"
             id="sectionLocation"
             tabindex="-1"
             :class="{
               'opacity-50 pointer-events-none select-none': !step3Unlocked,
               'ring-2 ring-rose-400/40': (touched.province && errors.province) || (touched.city && errors.city) || (touched.lokasi && errors.lokasi)
             }"
             class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none transition duration-200">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                   :class="step3Unlocked ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500'">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black transition-colors"
                  :class="step3Unlocked ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">3. Lokasi Acara</h2>
            </div>
            <span x-show="!step3Unlocked" class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Pilih Tanggal di Langkah 2</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Province Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '{{ old('province_code', '') }}', selectedText: '{{ old('province_code') ? ($provinces->firstWhere('code', old('province_code'))?->name ?? 'Pilih Provinsi') : 'Pilih Provinsi' }}' }"
                 @booking-province-set.window="selectedCode = $event.detail.code; selectedText = $event.detail.name; open = false">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Provinsi <span class="text-rose-500">*</span></label>
              <input type="hidden" name="province_code" :value="selectedCode">

              <div class="relative">
                <button type="button"
                        id="bookingProvinceBtn"
                        x-ref="provinceBtn"
                        @click="open = !open"
                        :disabled="!step3Unlocked || isDetectingLocation"
                        class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:outline-none focus:ring-2 flex items-center justify-between transition cursor-pointer disabled:cursor-not-allowed"
                        :class="[
                          touched.province && errors.province ? 'border-rose-400 focus:ring-rose-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400',
                          isDetectingLocation && !provinceCode ? 'opacity-70 animate-pulse' : ''
                        ]">
                  <div class="flex items-center gap-2 truncate">
                    <svg x-show="isDetectingLocation && !provinceCode" class="w-3.5 h-3.5 animate-spin text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="truncate" x-text="(isDetectingLocation && !provinceCode) ? 'Mencari provinsi...' : selectedText"></span>
                  </div>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                  @foreach($provinces as $prov)
                    @php
                      $provLat = !empty($prov->meta['lat']) ? (float)$prov->meta['lat'] : '';
                      $provLng = !empty($prov->meta['long']) ? (float)$prov->meta['long'] : '';
                    @endphp
                    <div @click="selectedCode = '{{ $prov->code }}'; selectedText = '{{ addslashes($prov->name) }}'; open = false; provinceCode = '{{ $prov->code }}'; touched.province = true; validateProvince(); fetchBookingCities('{{ $prov->code }}'); @if($provLat !== '' && $provLng !== '') panMapTo({{ $provLat }}, {{ $provLng }}, 9); @endif" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
                      {{ $prov->name }}
                    </div>
                  @endforeach
                </div>
              </div>
              <p x-show="touched.province && errors.province" x-text="errors.province" x-cloak class="text-[11px] font-bold text-rose-500 mt-1"></p>
            </div>

            <!-- City Custom Alpine Dropdown -->
            <div x-data="{ open: false, selectedCode: '{{ old('city_code', '') }}', selectedText: 'Pilih Kota / Kabupaten' }"
                 @booking-city-set.window="selectedCode = $event.detail.code; selectedText = $event.detail.name; open = false">
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kota / Kabupaten <span class="text-rose-500">*</span></label>
              <input type="hidden" name="city_code" :value="selectedCode" id="bookingCityInput" @change="cityCode = $event.target.value; selectedCode = $event.target.value; touched.city = true; validateCity()">

              <div class="relative">
                <button type="button"
                        id="bookingCityBtn"
                        x-ref="cityBtn"
                        @click="open = !open"
                        :disabled="!step3Unlocked || isDetectingLocation"
                        class="w-full px-4 py-2.5 text-xs font-medium text-left text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:outline-none focus:ring-2 flex items-center justify-between transition cursor-pointer disabled:cursor-not-allowed"
                        :class="[
                          touched.city && errors.city ? 'border-rose-400 focus:ring-rose-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400',
                          isDetectingLocation && !cityCode ? 'opacity-70 animate-pulse' : ''
                        ]">
                  <div class="flex items-center gap-2 truncate">
                    <svg x-show="isDetectingLocation && !cityCode" class="w-3.5 h-3.5 animate-spin text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="truncate" id="bookingCityLabel" x-text="(isDetectingLocation && !cityCode) ? 'Mencari kota...' : selectedText"></span>
                  </div>
                  <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1" id="bookingCityOptionsList">
                  <div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Pilih provinsi terlebih dahulu</div>
                </div>
              </div>
              <p x-show="touched.city && errors.city" x-text="errors.city" x-cloak class="text-[11px] font-bold text-rose-500 mt-1"></p>
            </div>
          </div>

          <!-- Peta Interaktif Titik Lokasi (Opsional, Mendukung Deteksi Satu Lokasi <= 500 m) -->
          <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <label class="text-xs font-bold text-gray-700 dark:text-gray-300">
                Pin Titik Lokasi di Peta (Opsional)
              </label>
              <div class="flex items-center gap-3">
                <button type="button"
                        @click="useCurrentLocation()"
                        :disabled="!step3Unlocked || isDetectingLocation"
                        class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:underline cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
                  <svg x-show="isDetectingLocation" class="w-3.5 h-3.5 animate-spin text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span x-text="isDetectingLocation ? 'Mencari Lokasi...' : 'Gunakan Lokasi Saat Ini'"></span>
                </button>
                <button type="button"
                        x-show="latitude !== '' && longitude !== ''"
                        x-cloak
                        @click="clearMarker()"
                        :disabled="!step3Unlocked || isDetectingLocation"
                        class="text-[11px] font-bold text-gray-400 hover:text-rose-500 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                  Hapus Pin
                </button>
              </div>
            </div>

            <!-- Search Bar Peta (Live Autocomplete Typeahead Pintar Ala Google Maps) -->
            <div class="flex items-center gap-2">
              <div class="relative flex-1 z-30">
                <input type="text"
                       x-model="mapQuery"
                       :disabled="!step3Unlocked || isDetectingLocation"
                       @input.debounce.300ms="searchMapLocation(true)"
                       @keydown.enter.prevent="searchMapLocation(false)"
                       :placeholder="isDetectingLocation ? 'Mendeteksi titik lokasi perangkat...' : 'Cari jalan, gedung, hotel, atau area acara...'"
                       class="w-full pl-4 pr-11 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none transition disabled:opacity-60"
                       :class="isDetectingLocation ? 'animate-pulse' : ''">
                
                <!-- Right Indicator: Live Spinner or Glass Icon -->
                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                  <svg x-show="mapSearching || isDetectingLocation" class="w-4 h-4 animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg x-show="!mapSearching && !isDetectingLocation" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                  </svg>
                </div>

                <!-- Floating Dropdown Hasil Rekomendasi Pintar (Ala Google Maps) -->
                <div x-show="mapResults.length > 0" 
                     x-cloak 
                     @click.away="mapResults = []"
                     class="absolute top-full left-0 right-0 z-[1050] mt-1.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl divide-y divide-gray-100 dark:divide-gray-700/80 max-h-72 overflow-y-auto">
                  <template x-for="(item, rIdx) in mapResults" :key="rIdx">
                    <button type="button"
                            @click="pickSearchResult(item)"
                            class="w-full text-left px-4 py-2.5 flex items-start gap-3 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition cursor-pointer group">
                      <div class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-amber-400 group-hover:text-gray-950 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                          <p class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 truncate" x-text="item.main_title"></p>
                          <span x-show="item.tag" x-text="item.tag" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300 shrink-0"></span>
                        </div>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5" x-text="item.sub_title"></p>
                      </div>
                    </button>
                  </template>
                </div>
              </div>

              <button type="button"
                      @click="searchMapLocation(false)"
                      :disabled="mapSearching || isDetectingLocation || !step3Unlocked"
                      class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-xs font-bold text-gray-800 dark:text-white transition shrink-0 cursor-pointer disabled:opacity-50">
                <span x-text="(mapSearching || isDetectingLocation) ? 'Mencari...' : 'Cari'"></span>
              </button>
            </div>

            <p x-show="mapNotice" x-text="mapNotice" x-cloak class="text-[11px] text-amber-600 dark:text-amber-400 font-medium"></p>

            <!-- Container Peta (Diperbesar Luas & Nyaman untuk Eksplorasi) -->
            <div x-ref="bookingMap" class="w-full h-80 sm:h-96 md:h-[420px] rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-gray-100 dark:bg-gray-900 shadow-xs" style="min-height: 380px;"></div>

            <p class="text-[11px] text-gray-500 dark:text-gray-400">
              <template x-if="latitude !== '' && longitude !== ''">
                <span>Titik pin terpilih: <strong class="text-gray-900 dark:text-white font-mono" x-text="latitude + ', ' + longitude"></strong>. Jeda persiapan dapat disesuaikan jika berdekatan dalam radius 500 meter pada paket yang mengizinkan.</span>
              </template>
              <template x-if="latitude === '' || longitude === ''">
                <span>Klik peta atau cari lokasi untuk menandai titik acara. Jika tidak ditandai, jadwal tetap dapat dipesan dengan perhitungan jeda penuh.</span>
              </template>
            </p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Alamat Lengkap / Nama Venue <span class="text-rose-500">*</span></label>
            <textarea name="lokasi_acara"
                    x-ref="lokasiInput"
                    x-model="lokasi_acara"
                    :disabled="!step3Unlocked || isDetectingLocation"
                    @input="touched.lokasi = true; validateLokasi()"
                    @blur="touched.lokasi = true; validateLokasi()"
                    rows="4"
                    :placeholder="isDetectingLocation ? 'Mengambil alamat lengkap dari titik lokasi...' : 'Masukkan nama venue, gedung, jalan, serta patokan lokasi acara'"
                    :class="[
                      touched.lokasi && errors.lokasi ? 'border-rose-400 focus:ring-rose-400 focus:border-rose-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400 focus:border-amber-400',
                      (!step3Unlocked || isDetectingLocation) ? 'opacity-60 cursor-not-allowed' : '',
                      isDetectingLocation ? 'animate-pulse' : ''
                    ]"
                    class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:ring-2 focus:outline-none transition leading-relaxed"></textarea>
            <p x-show="touched.lokasi && errors.lokasi" x-text="errors.lokasi" x-cloak class="text-[11px] font-bold text-rose-500 mt-1"></p>
          </div>
        </div>

        <!-- 4. Pilih Jam Mulai dan Selesai (2 Dropdown Terpisah Kelipatan 1 Jam) -->
        <div x-ref="timeSection"
             tabindex="-1"
             :class="{ 'opacity-50 pointer-events-none select-none': !step4Unlocked }"
             class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-5 outline-none transition duration-200">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                   :class="step4Unlocked ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500'">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black transition-colors"
                  :class="step4Unlocked ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">4. Pilih Jam Mulai dan Selesai</h2>
            </div>
            <span x-show="!step4Unlocked" class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Lengkapi Lokasi di Langkah 3</span>
            <span x-show="step4Unlocked && slotLoading" x-cloak class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Memeriksa slot...</span>
          </div>

          <template x-if="!step4Unlocked">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-200 dark:border-gray-700 text-center text-xs text-gray-500 dark:text-gray-400">
              <span x-show="!step1Done">Pilih paket layanan pada langkah 1 untuk memulai.</span>
              <span x-show="step1Done && !step2Done">Pilih tanggal sesi pada langkah 2 terlebih dahulu.</span>
              <span x-show="step2Done && !step3Done">Lengkapi lokasi acara pada langkah 3 untuk membuka pilihan jam.</span>
            </div>
          </template>

          <template x-if="step4Unlocked && !slotLoading && (!slotMatrix.is_open || !slotMatrix.has_available_slots)">
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-dashed border-gray-200 dark:border-gray-700 text-center text-xs text-gray-600 dark:text-gray-400" x-text="slotMatrix.reason || 'Tidak ada jam tersedia pada tanggal ini.'">
            </div>
          </template>

          <template x-if="step4Unlocked && (slotLoading || (slotMatrix.is_open && slotMatrix.has_available_slots))">
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Dropdown 1: Jam Mulai -->
                <div class="relative" @click.away="openStartDropdown = false">
                  <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                  <button type="button"
                          @click="openStartDropdown = !openStartDropdown; openEndDropdown = false"
                          :disabled="!step4Unlocked"
                          class="w-full px-4 py-2.5 text-xs font-bold text-left bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition cursor-pointer disabled:cursor-not-allowed"
                          :class="selectedStartTime ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500 font-medium'">
                    <span x-text="selectedStartTime ? selectedStartTime + ' WIB' : 'Pilih Jam Mulai'"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="openStartDropdown ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-show="openStartDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                    <template x-for="opt in startOptions" :key="opt.value">
                      <button type="button"
                              @click="pickStartTime(opt)"
                              :disabled="!opt.available"
                              :class="{
                                'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold': selectedStartTime === opt.value,
                                'text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 cursor-pointer': opt.available && selectedStartTime !== opt.value,
                                'text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-60': !opt.available
                              }"
                              class="w-full px-4 py-2.5 text-xs text-left flex items-center justify-between transition">
                        <span x-text="opt.label"></span>
                        <span x-show="!opt.available" class="text-[11px] font-medium text-gray-400">Penuh / Lewat</span>
                      </button>
                    </template>
                  </div>
                </div>

                <!-- Dropdown 2: Jam Selesai -->
                <div class="relative" @click.away="openEndDropdown = false">
                  <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                  <button type="button"
                          :disabled="!step4Unlocked || !selectedStartTime || validEndOptions.length === 0"
                          @click="if (step4Unlocked && selectedStartTime && validEndOptions.length > 0) { openEndDropdown = !openEndDropdown; openStartDropdown = false; }"
                          class="w-full px-4 py-2.5 text-xs font-bold text-left bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 flex items-center justify-between transition"
                          :class="[
                            (!step4Unlocked || !selectedStartTime || validEndOptions.length === 0) ? 'opacity-60 cursor-not-allowed' : 'hover:border-amber-400 cursor-pointer',
                            selectedEndTime ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500 font-medium'
                          ]">
                    <span x-text="selectedEndTime ? (selectedEndTime + ' WIB (' + selectedDurationHours + ' Jam)') : 'Pilih Jam Selesai'"></span>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="openEndDropdown ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-show="openEndDropdown" x-transition style="display: none;" class="absolute z-50 w-full mt-1.5 max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl py-1">
                    <template x-for="opt in validEndOptions" :key="opt.value">
                      <button type="button"
                              @click="pickEndTime(opt)"
                              :class="selectedEndTime === opt.value ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold' : 'text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600'"
                              class="w-full px-4 py-2.5 text-xs text-left flex items-center justify-between transition cursor-pointer">
                        <span x-text="opt.label"></span>
                      </button>
                    </template>
                  </div>
                </div>
              </div>

              <p x-show="selectedStartTime && selectedEndTime && selectedDurationHours > 0" x-cloak class="text-xs text-gray-600 dark:text-gray-300">
                Durasi pemotretan: <strong class="font-black text-gray-900 dark:text-white" x-text="selectedStartTime + ' - ' + selectedEndTime + ' WIB (' + selectedDurationHours + ' Jam)'"></strong>
              </p>
            </div>
          </template>

          <p x-show="touched.time && errors.time" x-text="errors.time" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
        </div>

        <!-- 5. Catatan Khusus -->
        <div x-ref="noteSection"
             tabindex="-1"
             :class="{ 'opacity-50 pointer-events-none select-none': !step5Unlocked }"
             class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs space-y-4 outline-none transition duration-200">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                   :class="step5Unlocked ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500'">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </div>
              <h2 class="text-sm sm:text-base font-black transition-colors"
                  :class="step5Unlocked ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'">5. Catatan Khusus</h2>
            </div>
            <span x-show="!step5Unlocked" class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Pilih Jam di Langkah 4</span>
            <span x-show="step5Unlocked" class="text-[11px] font-bold text-gray-400 dark:text-gray-500">Opsional</span>
          </div>

          <textarea name="catatan_khusus" :disabled="!step5Unlocked" rows="4" placeholder="Masukkan catatan konsep, jumlah peserta, atau instruksi khusus untuk fotografer" class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none transition leading-relaxed disabled:opacity-50 disabled:cursor-not-allowed">{{ old('catatan_khusus', '') }}</textarea>
        </div>

      </div>

      <!-- Kolom Kanan (5 Kolom): Ringkasan Pesanan -->
      <div class="lg:col-span-5 space-y-6 sticky top-28">
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-7 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-sm space-y-6">
          <h2 class="text-sm font-black text-gray-900 dark:text-white">Ringkasan Pesanan</h2>

          <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60">
            <x-user-avatar :user="$photographer->user" size="w-10 h-10" />
            <div class="flex-1 min-w-0">
              <h3 class="text-xs font-black text-gray-900 dark:text-white leading-snug truncate">{{ $photographer->nama }}</h3>
              <p class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                <span class="truncate">{{ $photographer->city->name ?? ($photographer->alamat ?: '-') }}</span>
              </p>
            </div>
          </div>

          <div x-show="!selectedServiceId" class="p-5 rounded-2xl bg-gray-50/70 dark:bg-gray-900/40 border border-dashed border-gray-200 dark:border-gray-700 text-center space-y-2">
            <h4 class="text-xs font-black text-gray-900 dark:text-white">Pilih Paket Layanan</h4>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 max-w-xs mx-auto leading-relaxed">Silakan tentukan paket layanan pada langkah 1 di sebelah kiri.</p>
          </div>

          <div x-show="selectedServiceId" x-transition class="p-4 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 space-y-1.5">
            <div class="flex items-center justify-between gap-2">
              <span class="text-[10px] font-black uppercase tracking-wider text-amber-800 dark:text-amber-300">Paket Terpilih</span>
              <span class="text-xs font-black text-amber-600 dark:text-amber-400" x-text="'Rp ' + hourlyRate.toLocaleString('id-ID') + ' / jam'"></span>
            </div>
            <h4 class="text-xs font-black text-gray-900 dark:text-white" x-text="selectedService ? selectedService.nama : ''"></h4>
            <p class="text-[11px] text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2" x-text="selectedService ? selectedService.deskripsi : ''"></p>
          </div>

          <div x-show="selectedDate" x-cloak class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700/60 space-y-1">
            <div class="flex items-center justify-between text-xs gap-2">
              <span class="text-gray-500 dark:text-gray-400">Jadwal Sesi:</span>
              <span class="font-bold text-amber-600 dark:text-amber-400"
                    x-text="(selectedStartTime && selectedEndTime) ? (selectedStartTime + ' - ' + selectedEndTime + ' WIB') : 'Pilih jam sesi'"></span>
            </div>
            <p class="text-[11px] text-gray-700 dark:text-gray-300 font-medium truncate" x-text="formatDateIndo(selectedDate)"></p>
          </div>

          <!-- Skema Pembayaran -->
          <div class="space-y-2.5">
            <span class="text-[11px] font-black text-gray-800 dark:text-gray-200 uppercase tracking-wider block">Skema Pembayaran</span>
            <input type="hidden" name="payment_type" :value="paymentType">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              <label @click="paymentType = 'full'"
                     :class="paymentType === 'full' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                     class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <input type="radio" class="sr-only peer" value="full" x-model="paymentType">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Bayar Penuh</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'full' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'full'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Pelunasan 100% langsung</p>
                <p class="text-xs font-black text-gray-900 dark:text-white pt-1" x-text="'Rp ' + totalBill.toLocaleString('id-ID')"></p>
              </label>

              <label @click="paymentType = 'dp'"
                     :class="paymentType === 'dp' ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"
                     class="p-3.5 rounded-xl border transition cursor-pointer select-none space-y-1">
                <input type="radio" class="sr-only peer" value="dp" x-model="paymentType">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-black">Uang Muka DP 30%</span>
                  <span class="w-3.5 h-3.5 rounded-full border-2 flex items-center justify-center shrink-0" :class="paymentType === 'dp' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600'">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500" x-show="paymentType === 'dp'"></span>
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Sisa 70% dilunasi kemudian</p>
                <p class="text-xs font-black text-amber-600 dark:text-amber-400 pt-1" x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></p>
              </label>
            </div>
          </div>

          <!-- Kalkulasi Biaya -->
          <div class="space-y-3 pt-1">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Tarif Per Jam</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + hourlyRate.toLocaleString('id-ID')">Rp 0</span>
            </div>

            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Durasi Sesi</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="selectedDurationHours > 0 ? selectedDurationHours + ' Jam' : '-'">-</span>
            </div>

            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
              <span>Total Biaya Layanan</span>
              <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + totalBill.toLocaleString('id-ID')">Rp 0</span>
            </div>

            <template x-if="paymentType === 'dp' && selectedServiceId">
              <div class="space-y-2.5">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                  <span>Estimasi DP (30%)</span>
                  <span class="font-bold text-gray-900 dark:text-white" x-text="'Rp ' + dpAmount.toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
                  <span>Sisa Pelunasan (70%)</span>
                  <span class="font-bold text-gray-800 dark:text-gray-200" x-text="'Rp ' + remainingAmount.toLocaleString('id-ID')"></span>
                </div>
              </div>
            </template>

            <div class="flex justify-between items-center text-gray-900 dark:text-white pt-2 border-t border-gray-100 dark:border-gray-700">
              <span class="text-xs font-bold">Total Biaya Sesi</span>
              <span class="text-xs font-black text-gray-900 dark:text-white" x-text="'Rp ' + currentPayAmount.toLocaleString('id-ID')">Rp 0</span>
            </div>
          </div>

          <!-- Tombol Ajukan Reservasi -->
          <div class="pt-2">
            <button type="submit"
                    :disabled="!isFormReady"
                    :class="!isFormReady ? 'opacity-50 cursor-not-allowed bg-gray-300 dark:bg-gray-700 text-gray-500' : 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-md cursor-pointer'"
                    class="w-full py-3.5 text-xs rounded-xl transition text-center block">
              <span x-show="!step1Done">Pilih Paket Layanan Terlebih Dahulu</span>
              <span x-show="step1Done && !step2Done" style="display: none;">Pilih Tanggal Sesi</span>
              <span x-show="step1Done && step2Done && !step3Done" style="display: none;">Lengkapi Lokasi Acara</span>
              <span x-show="step1Done && step2Done && step3Done && !step4Done" style="display: none;">Pilih Jam Mulai dan Selesai</span>
              <span x-show="isFormReady" style="display: none;">Ajukan Reservasi Jadwal</span>
            </button>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 text-center leading-relaxed pt-2">
              Belum ada pembayaran. Fotografer akan memeriksa jadwal terlebih dahulu sebelum tagihan diterbitkan.
            </p>
          </div>
        </div>
      </div>
    </form>

    <!-- Modal Booking Custom (Rule #59: Centered Title Without Icon, Centered 2 Action Buttons, No Top-Right X) -->
    <div x-show="customModalOpen"
         x-cloak
         @keydown.escape.window="customModalOpen = false"
         class="fixed inset-0 z-[9990] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
      <div @click.away="customModalOpen = false"
           class="bg-white dark:bg-gray-800 max-w-lg w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-5 overflow-visible">
        <div class="text-center space-y-1.5">
          <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">Ajukan Booking Custom</h3>
          <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
            Kirim rencana jadwal multi-hari berurutan atau jam khusus ke obrolan fotografer untuk disepakati bersama.
          </p>
        </div>

        <form action="{{ route('booking.custom-request') }}" method="POST" @submit="submitCustomRequest($event)" data-no-confirm="true" novalidate class="space-y-4">
          @csrf
          <input type="hidden" name="photographer_id" value="{{ $photographer->id }}">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_mulai" min="{{ date('Y-m-d') }}" x-model="customForm.tanggal_mulai"
                     class="w-full px-3.5 py-2.5 text-xs font-bold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
              <input type="date" name="tanggal_selesai" :min="customForm.tanggal_mulai || '{{ date('Y-m-d') }}'" x-model="customForm.tanggal_selesai"
                     class="w-full px-3.5 py-2.5 text-xs font-bold text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Perkiraan Jam Mulai</label>
              <x-time-dropdown
                model="customForm.jam_mulai"
                name="jam_mulai"
                placeholder="Opsional"
                align="left"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Perkiraan Jam Selesai</label>
              <x-time-dropdown
                model="customForm.jam_selesai"
                name="jam_selesai"
                placeholder="Opsional"
                align="md-right"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Lokasi Acara <span class="text-rose-500">*</span></label>
            <input type="text" name="lokasi" x-model="customForm.lokasi" placeholder="Masukkan kota atau alamat venue acara"
                   class="w-full px-4 py-2.5 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Kebutuhan Acara <span class="text-rose-500">*</span></label>
            <textarea name="kebutuhan" rows="4" x-model="customForm.kebutuhan" placeholder="Jelaskan jenis acara, susunan hari, atau kebutuhan khusus lainnya"
                      class="w-full px-4 py-3 text-xs font-medium text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none leading-relaxed"></textarea>
          </div>

          <p x-show="customError" x-text="customError" x-cloak class="text-[11px] font-bold text-red-500 text-center"></p>

          <div class="flex items-center justify-center gap-3 pt-1">
            <button type="button"
                    @click="customModalOpen = false"
                    class="py-2.5 px-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer">
              Batal
            </button>
            <button type="submit"
                    class="py-2.5 px-6 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
              Kirim ke Chat
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  function fetchBookingCities(provinceCode, preselectCityCode = '', targetCityName = '') {
    const cityInput = document.getElementById('bookingCityInput');
    const cityList = document.getElementById('bookingCityOptionsList');

    if (!preselectCityCode && !targetCityName) {
      cityInput.value = '';
      cityInput.dispatchEvent(new Event('change'));
      window.dispatchEvent(new CustomEvent('booking-city-set', { detail: { code: '', name: 'Memuat Kota...' } }));
    }
    cityList.innerHTML = '<div class="px-4 py-2.5 text-xs text-gray-400 font-semibold">Memuat data kota...</div>';

    fetch('/api/cities/' + provinceCode)
      .then(res => res.json())
      .then(data => {
        if (!preselectCityCode && !targetCityName) {
          window.dispatchEvent(new CustomEvent('booking-city-set', { detail: { code: '', name: 'Pilih Kota / Kabupaten' } }));
        }
        let html = '';
        let matchedCity = null;

        const cleanTarget = targetCityName ? targetCityName.toLowerCase().replace(/^(kabupaten|kota|kab\.)\s+/i, '').replace(/[^a-z0-9]/g, '') : '';

        data.forEach(city => {
          if (preselectCityCode && String(city.code) === String(preselectCityCode)) {
            matchedCity = city;
          } else if (!matchedCity && cleanTarget) {
            const cleanCityName = city.name.toLowerCase().replace(/^(kabupaten|kota|kab\.)\s+/i, '').replace(/[^a-z0-9]/g, '');
            if (cleanCityName === cleanTarget || cleanCityName.includes(cleanTarget) || cleanTarget.includes(cleanCityName)) {
              matchedCity = city;
            }
          }

          const cityLat = (city.meta && city.meta.lat) ? city.meta.lat : '';
          const cityLng = (city.meta && city.meta.long) ? city.meta.long : '';
          const escapedName = city.name.replace(/'/g, "\\'");
          html += `
            <div onclick="selectBookingCity('${city.code}', '${escapedName}', '${cityLat}', '${cityLng}', false)" class="px-4 py-2.5 text-xs text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition cursor-pointer">
              ${city.name}
            </div>
          `;
        });
        cityList.innerHTML = html;

        if (matchedCity) {
          const mLat = (matchedCity.meta && matchedCity.meta.lat) ? matchedCity.meta.lat : '';
          const mLng = (matchedCity.meta && matchedCity.meta.long) ? matchedCity.meta.long : '';
          selectBookingCity(matchedCity.code, matchedCity.name, mLat, mLng, Boolean(targetCityName));
        }
      });
  }

  function selectBookingCity(code, name, lat = '', lng = '', skipPan = false) {
    const input = document.getElementById('bookingCityInput');
    input.value = code;
    input.dispatchEvent(new Event('change'));
    window.dispatchEvent(new CustomEvent('booking-city-set', { 
      detail: { 
        code: code, 
        name: name,
        lat: lat,
        lng: lng,
        skipPan: skipPan
      } 
    }));
  }
</script>
@endsection
