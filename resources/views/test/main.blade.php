<!DOCTYPE html>
<html lang="id">
    @include('test.layout.header')
    <body>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <!-- Progress -->
                    {{-- <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 40%"></div>
                    </div> --}}

                    @yield('content')
                    
                    <p class="text-center mt-4 text-muted small">Dibuat oleh M Ade Maulana, S.Kom - Versi 1.0 Beta</p>
                </div>
            </div>
        </div>

    @include('test.layout.footer')

    </body>
</html>