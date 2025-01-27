@props(['id', 'url' , 'maxSize' => 5])
<form>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-lg-4 mx-auto col-md-9 col-sm-12">
                <div class="dropzone dropzone-default dropzone-primary" id="{{ $id }}">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Drop XLSX files here or click to upload.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        "use strict";
        var KTDropzoneDemo = function () {
            var fn = function () {
                $('#'+"{{$id}}").dropzone({
                    url: "{{ $url }}",
                    paramName: "file",
                    maxFiles: 10,
                    maxFilesize: "{{ $maxSize }}", // MB
                    addRemoveLinks: true,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    accept: function(file, done) {
                        done();
                    },
                    error: function (file, response) {
                        const {errors} = response;

                        @this.call('setErrors', errors)
                    }
                });
            }
            return {
                init: function() {
                    fn();
                }
            };
        }();

        KTUtil.ready(function() {
            KTDropzoneDemo.init();
        });

    </script>
@endpush
