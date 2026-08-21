<!-- Shared Interactive Review & Rating Modal Component -->
<div x-data="reviewModalApp()"
     @open-review-modal.window="openModal($event.detail)"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop overlay -->
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeModal()"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 dark:border-gray-700/80">

            <form method="POST" action="{{ route('client.reviews.store') }}" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="id_contract" :value="contractId">
                <input type="hidden" name="rating" :value="rating">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center font-black text-sm">
                            ⭐
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-gray-900 dark:text-white" id="modal-title">Beri Ulasan & Rating Studio</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="photoName || 'Studio Fotografer'"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Star Rating Picker -->
                <div class="text-center space-y-2 py-2">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">Pilih Kepuasan Layanan (1 - 5 Bintang)</label>
                    <div class="flex items-center justify-center gap-2">
                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                            <button type="button" 
                                    @mouseenter="hoverRating = star" 
                                    @mouseleave="hoverRating = rating"
                                    @click="setRating(star)"
                                    class="p-1 transition-transform hover:scale-125 focus:outline-none">
                                <svg class="w-9 h-9 transition-colors" 
                                     :class="(hoverRating || rating) >= star ? 'text-amber-400 fill-amber-400 drop-shadow-sm' : 'text-gray-200 dark:text-gray-700 fill-transparent'"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    <div class="text-xs font-extrabold text-amber-600 dark:text-amber-400 h-4" x-text="getRatingLabel(hoverRating || rating)"></div>
                </div>

                <!-- Written Testimonial Textarea -->
                <div class="space-y-1.5 text-left">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Ulasan Testimoni Anda <span class="text-amber-500">*</span>
                    </label>
                    <textarea name="deskripsi_review" 
                              x-model="reviewText" 
                              rows="4" 
                              required
                              placeholder="Bagikan pengalaman Anda mengenai kualitas foto, ketepatan waktu, dan keramahan fotografer..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="closeModal()" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="!reviewText.trim() || reviewText.length < 5"
                            class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-extrabold text-xs shadow-md transition">
                        Kirim Ulasan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function reviewModalApp() {
    return {
        isOpen: false,
        contractId: null,
        photoName: '',
        serviceName: '',
        rating: 5,
        hoverRating: 5,
        reviewText: '',
        openModal(detail) {
            if (detail) {
                this.contractId = detail.contractId || null;
                this.photoName = detail.photoName || '';
                this.serviceName = detail.serviceName || '';
                this.rating = 5;
                this.hoverRating = 5;
                this.reviewText = '';
            }
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
        },
        setRating(val) {
            this.rating = val;
            this.hoverRating = val;
        },
        getRatingLabel(val) {
            const labels = {
                5: '⭐⭐⭐⭐⭐ Sangat Memuaskan & Sempurna!',
                4: '⭐⭐⭐⭐ Bagus & Memuaskan',
                3: '⭐⭐⭐ Cukup Baik',
                2: '⭐⭐ Kurang Memuaskan',
                1: '⭐ Kecewa / Perlu Perbaikan'
            };
            return labels[val] || '';
        }
    };
}
</script>