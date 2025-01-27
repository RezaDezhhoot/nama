<div>
    @if ($errors->any())
        <div class="alert alert-custom alert-notice alert-light-danger fade show mb-5" role="alert">
            <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i></div>
            <div class="alert-text">
                <h5>{{ __('general.messages.invalid_data') }}</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="نزدیک">
                    <span aria-hidden="true"><i class="ki ki-close" wire:click="resetErrors"></i></span>
                </button>
            </div>
        </div>
    @endif
</div>
