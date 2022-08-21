<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
   
    <meta name="author" content="" />
    <title>Coinpayment withdrawal system</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />

    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet"
        type="text/css" />
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet"
        type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
</head>

<body>
    <!-- Navigation-->
    <nav class="navbar navbar-dark bg-dark static-top container">
        <div class="container">
            <a class="navbar-brand" href="#!">Coinpayment Withdrawal System</a>

        </div>
    </nav>

    <!-- Icons Grid-->


    <section class="testimonials container  bg-light">
        <div class="container">
            <div class="row">

                <div class="col-lg-6 ">
                    <h2 class="mb-5 text-center ">Coin Balance </h2>
                    <hr>
                    {{-- return view('welcome',compact('output','home_error','balance_error','coin_array')) ; --}}
                    @if ($home_error == '' && $output != '' && $balance_error == false)
                        {!! $output !!}
                    @else
                        <h6 class="alert alert-warning dismiss text-center"><strong>Something Went Wrong! Please contact
                                the Developer</strong></h6>
                    @endif


                </div>
                <div class="col-lg-6 ">
                    <h2 class="mb-5 text-center ">Withdrawal Form</h2>
                    <hr>
                    <form action="{{ route('post.withdraw') }}" id="withdrawForm" method="post">
                        <div class="row">
                            <div class="col-lg-12 form-group  mb-3">
                                <label for=""> * Select Coin</label>
                                <select name="currency" class="form-control">
                                    <option value="">
                                        *-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
                                    </option>
                                    @foreach ($coin_array as $key => $value)
                                        {{-- @if ($value == 'LTCT') --}}
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        {{-- @endif --}}
                                    @endforeach
                                </select>
                                @csrf
                            </div>
                            <div class="col-lg-12 form-group mb-3">
                                <label for=""> *Destination Address</label>
                                <input class="form-control" name="address" type="text"
                                    placeholder="Your Destination Address">

                            </div>
                            <div class="col-lg-12 form-group  mb-3">
                                <label for=""> *Amount</label>
                                <input class="form-control" name="amount" type="text" min="0"   placeholder="0.00000000">

                            </div>

                            <div class="col-lg-12 form-group">
                                <br>
                                <button class="btn-primary btn width-100" style="width:100%" id="withdrawBtn"
                                    type="submit"> Submit</button>

                            </div>


                        </div>
                    </form>

                </div>


            </div>

        </div>
    </section>

    <!-- Footer-->
    <footer class="footer bg-light container text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 h-100 text-center text-lg-start my-auto">

                    <p class="text-muted small mb-4 mb-lg-0 text-center">&copy; {{ date('Y') }}. All Rights
                        Reserved.</p>
                </div>

            </div>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>t>
    <!-- Core theme JS-->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    <script>
        $(document).ready(function() {

            $('#withdrawForm').submit(function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var data = $(this).serialize();
                $('#withdrawBtn').prop('disabled', true).html('Processing........')
                $.ajax({
                    type: "post",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        if(data.status==1){
                            swal({
                                title: data.msg,
                                text: "",
                                icon: "success",
                                buttons: true,
                                dangerMode: true,
                            })
                            .then((willDelete) => {
                                if (willDelete) {
                                    location.reload();
                                } else {
                                    location.reload();
                                }
                            });

                        }else{
                            swal({
                                title: data.msg,
                                text: "",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            })
                            .then((willDelete) => {
                                if (willDelete) {

                                } else {

                                }
                            });


                        }

                        $('#withdrawBtn').prop('disabled', false).html('Submit')
                    }
                });

            });


            //             <div class="alert alert-danger d-flex align-items-center" role="alert">
            //   <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
            //   <div>
            //     An example danger alert with an icon
            //   </div>
            // </div>
        });
    </script>
</body>

</html>
