<div
    x-data="{
        show: false,
        title: '',
        message: '',
        action: '',
        method: 'DELETE',
        confirm(data) {
            this.title = data.title;
            this.message = data.message;
            this.action = data.action;
            this.method = data.method || 'DELETE';
            this.show = true;
        }
    }"
    @open-confirm.window="confirm($event.detail)"
>
    <x-modal name="confirm-modal" x-bind:open="show">
        <div class="flex flex-col items-center text-center py-4">
            <div class="w-16 h-16 rounded-full badge-brand flex items-center justify-center mb-4">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-brand" />
            </div>
            
            <h3 class="text-lg font-bold text-ink mb-1" x-text="title"></h3>
            <p class="text-sm text-ink-muted mb-6" x-text="message"></p>
            
            <form x-bind:action="action" method="POST" class="w-full flex gap-3">
                @csrf
                <input type="hidden" name="_method" x-bind:value="method">
                <button type="button" @click="$dispatch('close-modal', 'confirm-modal'); show = false" class="btn-secondary flex-1">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary flex-1">
                    Confirmar
                </button>
            </form>
        </div>
    </x-modal>
</div>
