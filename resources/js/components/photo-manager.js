export default () => ({
    uploadModalOpen: false,
    deleteModalOpen: false,
    deleteUrl: '',
    lightboxOpen: false,
    currentIndex: 0,
    allPhotos: [],
    captionModalOpen: false,
    editingCaptionId: null,
    editingCaptionText: '',

    init() {
        // We use a small delay to ensure DOM is ready if called from x-init
        this.$nextTick(() => {
            const input = document.getElementById('photos-upload');
            const list = document.getElementById('file-list');
            if (input && list) {
                input.addEventListener('change', () => {
                    list.innerHTML = Array.from(input.files).map(f => `<div class="bg-brand-50 p-2 rounded-lg truncate">• ${f.name}</div>`).join('');
                });
            }
        });
    },

    openUploadModal() { this.uploadModalOpen = true; },

    confirmDelete(url) {
        this.deleteUrl = url;
        this.deleteModalOpen = true;
    },

    editCaption(id, currentCaption) {
        this.editingCaptionId = id;
        this.editingCaptionText = currentCaption;
        this.captionModalOpen = true;
    },

    async saveCaption() {
        try {
            const response = await fetch(`/admin/gallery/photo/${this.editingCaptionId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ caption: this.editingCaptionText })
            });

            if (response.ok) {
                const captionEl = document.getElementById(`photo-caption-${this.editingCaptionId}`);
                if (captionEl) captionEl.textContent = this.editingCaptionText || 'Untitled Asset';

                this.captionModalOpen = false;
                this.showFlash('Caption updated successfully.');

                // Update allPhotos for lightbox refresh
                const photo = this.allPhotos.find(p => p.id === this.editingCaptionId);
                if (photo) photo.caption = this.editingCaptionText || 'No caption provided';
            }
        } catch (error) {
            console.error('Save failed:', error);
            alert('Failed to save caption.');
        }
    },

    openLightbox(index, photos) {
        this.allPhotos = photos;
        this.currentIndex = index;
        this.lightboxOpen = true;
    },

    prevPhoto() {
        this.currentIndex = (this.currentIndex - 1 + this.allPhotos.length) % this.allPhotos.length;
    },

    nextPhoto() {
        this.currentIndex = (this.currentIndex + 1) % this.allPhotos.length;
    },

    get currentPhoto() {
        return this.allPhotos[this.currentIndex] || { url: '', caption: '' };
    },

    async executeDelete() {
        try {
            const response = await fetch(this.deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();
            if (response.ok) {
                this.deleteModalOpen = false;
                this.showFlash(data.success || 'Deleted successfully');
                // Use the router to reload if available, or fallback to location.reload
                if (window.router && typeof window.router.reload === 'function') {
                    window.router.reload();
                } else {
                    setTimeout(() => window.location.reload(), 1000);
                }
            }
        } catch (error) {
            console.error('Delete failed:', error);
            alert('Failed to delete photo.');
        }
    },

    showFlash(message) {
        // Dispatch global toast event if available
        if (window.showToast) {
            window.showToast(message);
            return;
        }

        const flash = document.getElementById('flash-message');
        if (!flash) return;
        flash.innerHTML = `<div class="fixed top-8 right-8 z-[300] bg-gray-900 border border-white/10 backdrop-blur-xl text-white px-10 py-5 rounded-[2rem] shadow-2xl animate-in slide-in-from-right duration-500 flex items-center gap-6">
            <div class="p-3 bg-green-500 rounded-2xl shadow-lg shadow-green-500/20"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg></div>
            <div class="flex flex-col text-left">
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Archival Record Updated</span>
                <span class="font-black uppercase text-[11px] tracking-widest">${message}</span>
            </div>
        </div>`;
        setTimeout(() => flash.innerHTML = '', 4000);
    }
});
