@section('title','ورود به مدیریت سامانه نما')
<div wire:init="loadRecaptcha" class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7">
    <div class="mx-auto flex-center col-md-6 col-12">
        <div class="login-form login-signin">
            <form wire:submit.prevent="login" class="form" novalidate="novalidate" id="kt_login_signin_form">
                <div class="pb-13 pt-lg-0 pt-5">
                    <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">ورود به مدیریت آرمان</h3>
                </div>
                <div class="form-group">
                    <label class="font-size-h6 font-weight-bolder text-dark">شماره همراه/ادرس ایمیل</label>
                    <input wire:model.defer="email" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="text" name="username" autocomplete="off" />
                    <x-admin.error key="email" />
                </div>
                <div class="form-group">
                    <div class="d-flex justify-content-between mt-n5">
                        <label class="font-size-h6 font-weight-bolder text-dark pt-5">رمز عبور</label>
                    </div>
                    <input wire:model.defer="password" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" type="password" name="password" autocomplete="off" />
                    <x-admin.error key="password" />
                </div>
                <div class="form-group">
                    <div class="input-box p-0 overflow-hidden">
                        <div class="g-recaptcha d-inline-block"
                             data-sitekey="{{ config('services.recaptcha.site_key') }}"
                             data-callback="reCaptchaCallback" wire:ignore></div>
                        @error('recaptcha')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="pb-lg-0 pb-5">
                    <button wire:loading.attr="disabled" type="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">
                        ورود به ادمین
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')

    <script>
        function reCaptchaCallback(response) {
        @this.set('recaptcha', response);
        }

        function back_to_episode(id)
        {
            $('html, body').animate({
                scrollTop: $(`#episode${id}`).offset().top
            }, 1000);
        }

        Livewire.on('resetReCaptcha', () => {
            grecaptcha.reset();
        });

        Livewire.on('loadRecaptcha', () => {
            const script = document.createElement('script');

            script.setAttribute('src', 'https://www.google.com/recaptcha/api.js');

            const start = document.createElement('script');


            document.body.appendChild(script);
            document.body.appendChild(start);
        });
    </script>
@endpush
