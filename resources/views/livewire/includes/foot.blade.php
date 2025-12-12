<script>
    var KTAppSettings = {
        "breakpoints": {
            "sm": 576,
            "md": 768,
            "lg": 992,
            "xl": 1200,
            "xxl": 1400
        },
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };
</script>
<script src="{{asset('admin/js/sweetalert2@11.js')}}"></script>
<script src="/admin/plugins/global/plugins.bundle.js?v=7.2.9"></script>
<script src="/admin/js/scripts.bundle.js?v=7.2.9"></script>
<script src="{{asset('admin/plugins/custom/datepicker/persian-date.min.js')}}"></script>
<script src="{{asset('admin/plugins/custom/datepicker/persian-datepicker.min.js')}}"></script>


<script src="{{asset('admin/js/select2.min.js')}}"></script>

<script>

    document.addEventListener('livewire:init', () => {
        Livewire.on('showModal', function ([data]) {
            const id = '#' + data + 'Modal';
            $(id).modal('show');
        })

        Livewire.on('hideCollapse', function ([data]) {
            console.log(data)
            const id = '#' + data;
            $(id).collapse('hide');
        })

        Livewire.on('hideModal', function ([data]) {
            const id = '#' + data + 'Modal';
            $(id).modal('hide');

        })

        Livewire.on('notify', data => {
            Swal.fire({
                position: 'top-end',
                icon: data[0].icon,
                title: data[0].title,
                showConfirmButton: false,
                timer: 4000,
                toast: true,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
        })
        Livewire.on('message', ([data]) => {
            Swal.fire( data);
        })
    })


    $.fn.modal.Constructor.prototype.enforceFocus = function() {
        modal_this = this
        $(document).on('focusin.modal', function (e) {
            if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select')
                && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
                modal_this.$element.focus()
            }
        })
    };
</script>
@stack('scripts')
