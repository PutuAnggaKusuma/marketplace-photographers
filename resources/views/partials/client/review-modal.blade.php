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

            <form method="POST" :action="formAction" @submit="submitReviewForm($event)" novalidate class="p-6 sm:p-7 flex flex-col gap-5">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="id_contract" :value="contractId">
                <input type="hidden" name="rating" :value="rating">

                <!-- Close 'X' Button in Top Right (Only when isEdit is true; Batal button is removed below to avoid 3 buttons horizontally) -->
                <button type="button" 
                        x-show="isEdit"
                        @click="closeModal()" 
                        class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/60 cursor-pointer"
                        title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Header (No Icon, Centered Title) -->
                <div class="border-b border-gray-100 dark:border-gray-700 pb-4 text-center flex flex-col gap-1">
                    <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white" id="modal-title" x-text="isEdit ? 'Ulasan Fotografer' : 'Beri Ulasan Fotografer'"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="photoName || 'Fotografer'"></p>
                </div>

                <!-- Star Rating Picker -->
                <div class="text-center flex flex-col gap-2 py-2">
                    <label class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pilih Kepuasan Layanan (1 - 5 Bintang) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center justify-center gap-2">
                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                            <button type="button" 
                                    @mouseenter="hoverRating = star" 
                                    @mouseleave="hoverRating = rating"
                                    @click="setRating(star)"
                                    class="p-1 transition-transform hover:scale-125 focus:outline-none cursor-pointer">
                                <svg class="w-9 h-9 transition-colors" 
                                     :class="(hoverRating || rating) >= star ? 'text-amber-400 fill-amber-400 drop-shadow-sm' : 'text-gray-300 dark:text-gray-600 fill-transparent'"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    <div class="text-xs font-extrabold h-4 flex items-center justify-center gap-1.5">
                        <template x-if="(hoverRating || rating) > 0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-900 dark:text-white" x-text="getRatingLabelText(hoverRating || rating)"></span>
                                <span class="text-amber-500 dark:text-amber-400" x-text="'(' + (hoverRating || rating) + '/5)'"></span>
                            </div>
                        </template>
                        <template x-if="(hoverRating || rating) === 0">
                            <span class="text-gray-400 dark:text-gray-500 font-normal">Pilih jumlah bintang kepuasan (1 - 5)</span>
                        </template>
                    </div>
                    <p x-show="touched.rating && errors.rating" x-text="errors.rating" x-cloak class="text-[11px] font-bold text-red-500"></p>
                </div>

                <!-- Written Testimonial Textarea -->
                <div class="flex flex-col gap-1.5 text-left">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Ulasan Testimoni Anda <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi_review" 
                              x-model="reviewText" 
                              x-ref="reviewInput"
                              @input="touched.reviewText = true; validateReviewText()"
                              @blur="touched.reviewText = true; validateReviewText()"
                              rows="4" 
                              placeholder="Bagikan pengalaman Anda mengenai kualitas foto, ketepatan waktu, dan keramahan fotografer..."
                              :class="touched.reviewText && errors.reviewText ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'"
                              class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none transition resize-none"></textarea>
                    <p x-show="touched.reviewText && errors.reviewText" x-text="errors.reviewText" x-cloak class="text-[11px] font-bold text-red-500"></p>
                </div>

                <!-- Modal Actions (Always Centered, Maximum 2 Buttons, Never 3 Buttons Horizontally) -->
                <div class="flex items-center justify-center gap-3">
                    <template x-if="!isEdit">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">
                            Batal
                        </button>
                    </template>

                    <template x-if="isEdit">
                        <button type="button" 
                                @click="deleteReview()"
                                class="px-5 py-2.5 rounded-xl border border-rose-200 dark:border-rose-800/80 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold text-xs transition cursor-pointer">
                            Hapus Ulasan
                        </button>
                    </template>

                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition cursor-pointer">
                        <span x-text="isEdit ? 'Perbaharui Data' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>

            <!-- Hidden Delete Review Form -->
            <form id="delete-review-form" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
            </form>

        </div>
    </div>
</div>

<script>
function reviewModalApp() {
    return {
        isOpen: false,
        isEdit: false,
        contractId: null,
        testimonialId: null,
        photoName: '',
        serviceName: '',
        rating: 0,
        hoverRating: 0,
        reviewText: '',
        formAction: '{{ route('client.reviews.store') }}',
        touched: {
            rating: false,
            reviewText: false
        },
        errors: {
            rating: '',
            reviewText: ''
        },
        openModal(detail) {
            if (detail) {
                this.contractId = detail.contractId || null;
                this.testimonialId = detail.testimonialId || null;
                this.photoName = detail.photoName || '';
                this.serviceName = detail.serviceName || '';
                this.isEdit = !!detail.isEdit;
                this.rating = this.isEdit && detail.rating ? Number(detail.rating) : 0;
                this.hoverRating = this.rating;
                this.reviewText = detail.reviewText || '';
                this.formAction = this.isEdit 
                    ? `/client/reviews/${this.testimonialId}` 
                    : '{{ route('client.reviews.store') }}';
            }
            this.touched = { rating: false, reviewText: false };
            this.errors = { rating: '', reviewText: '' };
            this.isOpen = true;
        },
        closeModal() {
            this.isOpen = false;
        },
        deleteReview() {
            if (!this.testimonialId) return;
            const targetId = this.testimonialId;
            this.closeModal();
            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: {
                    title: 'Hapus Ulasan dan Rating?',
                    message: 'Apakah Anda yakin ingin menghapus ulasan dan bintang penilaian ini secara permanen?',
                    confirmText: 'Ya, Hapus Ulasan',
                    type: 'danger',
                    actionUrl: '/client/reviews/' + targetId,
                    method: 'DELETE'
                }
            }));
        },
        setRating(val) {
            this.rating = val;
            this.hoverRating = val;
            this.touched.rating = true;
            this.validateRating();
        },
        validateRating() {
            if (!this.rating || this.rating < 1) {
                this.errors.rating = 'Silakan pilih minimal 1 bintang kepuasan.';
                return false;
            }
            this.errors.rating = '';
            return true;
        },
        validateReviewText() {
            if (!this.reviewText || !this.reviewText.trim()) {
                this.errors.reviewText = 'Ulasan testimoni wajib diisi.';
                return false;
            }
            if (this.reviewText.trim().length < 5) {
                this.errors.reviewText = 'Ulasan testimoni minimal 5 karakter.';
                return false;
            }
            this.errors.reviewText = '';
            return true;
        },
        submitReviewForm(e) {
            this.touched.rating = true;
            this.touched.reviewText = true;

            const isRatingValid = this.validateRating();
            const isTextValid = this.validateReviewText();

            if (!isRatingValid || !isTextValid) {
                e.preventDefault();
                if (!isRatingValid) {
                    // focus rating or notify
                } else if (!isTextValid && this.$refs.reviewInput) {
                    this.$refs.reviewInput.focus();
                }
                return false;
            }
            return true;
        },
        getRatingLabelText(val) {
            const labels = {
                5: 'Sangat Memuaskan & Sempurna',
                4: 'Bagus & Memuaskan',
                3: 'Cukup Baik',
                2: 'Kurang Memuaskan',
                1: 'Kecewa / Perlu Perbaikan'
            };
            return labels[val] || '';
        }
    };
}
</script>