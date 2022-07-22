@extends('layouts.app')

@section('content')
<main>

    <div class="row g-3">
        <div class="col-xl-4 order-xl-1">
            <div class="card">
                <div class="card-header bg-light btn-reveal-trigger d-flex flex-between-center">
                    <h5 class="mb-0">Order Summary</h5><a class="btn btn-link btn-sm btn-reveal text-600"
                        href="shopping-cart.html"><svg class="svg-inline--fa fa-pencil-alt fa-w-16" aria-hidden="true"
                            focusable="false" data-prefix="fas" data-icon="pencil-alt" role="img"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M497.9 142.1l-46.1 46.1c-4.7 4.7-12.3 4.7-17 0l-111-111c-4.7-4.7-4.7-12.3 0-17l46.1-46.1c18.7-18.7 49.1-18.7 67.9 0l60.1 60.1c18.8 18.7 18.8 49.1 0 67.9zM284.2 99.8L21.6 362.4.4 483.9c-2.9 16.4 11.4 30.6 27.8 27.8l121.5-21.3 262.6-262.6c4.7-4.7 4.7-12.3 0-17l-111-111c-4.8-4.7-12.4-4.7-17.1 0zM124.1 339.9c-5.5-5.5-5.5-14.3 0-19.8l154-154c5.5-5.5 14.3-5.5 19.8 0s5.5 14.3 0 19.8l-154 154c-5.5 5.5-14.3 5.5-19.8 0zM88 424h48v36.3l-64.5 11.3-31.1-31.1L51.7 376H88v48z">
                            </path>
                        </svg><!-- <span class="fas fa-pencil-alt"></span> Font Awesome fontawesome.com --></a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless fs--1 mb-0">
                        <tbody>
                            <tr class="border-bottom">
                                <th class="ps-0 pt-0">Apple MacBook Pro 15" x 1<div class="text-400 fw-normal fs--2">
                                        Z0V20008N: 2.9GHz 6-core 8th-Gen Intel Core i9, 32GB RAM</div>
                                </th>
                                <th class="pe-0 text-end pt-0">$1345</th>
                            </tr>
                            <tr class="border-bottom">
                                <th class="ps-0">Apple iMac Pro x 1<div class="text-400 fw-normal fs--2">27-inch with
                                        Retina 5K Display, 3.0GHz 10-core Intel Xeon W, 1TB</div>
                                </th>
                                <th class="pe-0 text-end">$2010</th>
                            </tr>
                            <tr class="border-bottom">
                                <th class="ps-0">Subtotal</th>
                                <th class="pe-0 text-end">$3355</th>
                            </tr>
                            <tr class="border-bottom">
                                <th class="ps-0">Coupon: <span class="text-success">40SITEWIDE</span></th>
                                <th class="pe-0 text-end">-$55</th>
                            </tr>
                            <tr class="border-bottom">
                                <th class="ps-0">Shipping</th>
                                <th class="pe-0 text-end">$20</th>
                            </tr>
                            <tr>
                                <th class="ps-0 pb-0">Total</th>
                                <th class="pe-0 text-end pb-0">$3320</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between bg-light">
                    <div class="fw-semi-bold">Payable Total</div>
                    <div class="fw-bold">$3320</div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <div class="row flex-between-center">
                        <div class="col-sm-auto">
                            <h5 class="mb-2 mb-sm-0">Your Shipping Address</h5>
                        </div>
                        <div class="col-sm-auto"><a class="btn btn-falcon-default btn-sm" href="#!"><svg
                                    class="svg-inline--fa fa-plus fa-w-14 me-2" data-fa-transform="shrink-2"
                                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""
                                    style="transform-origin: 0.4375em 0.5em;">
                                    <g transform="translate(224 256)">
                                        <g transform="translate(0, 0)  scale(0.875, 0.875)  rotate(0 0 0)">
                                            <path fill="currentColor"
                                                d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"
                                                transform="translate(-224 -256)"></path>
                                        </g>
                                    </g>
                                </svg>
                                <!-- <span class="fas fa-plus me-2" data-fa-transform="shrink-2"></span> Font Awesome fontawesome.com -->Add
                                New Address </a></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="form-check mb-0 custom-radio radio-select"><input class="form-check-input"
                                    id="address-1" type="radio" name="clientName" checked="checked"><label
                                    class="form-check-label mb-0 fw-bold d-block" for="address-1">Antony Hopkins<span
                                        class="radio-select-content"><span> 2392 Main Avenue,<br>Pensaukee,<br>New
                                            Jersey 02139<span class="d-block mb-0 pt-2">+(856)
                                                929-229</span></span></span></label><a class="fs--1" href="#!">Edit</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-relative">
                                <div class="form-check mb-0 custom-radio radio-select"><input class="form-check-input"
                                        id="address-2" type="radio" name="clientName"><label
                                        class="form-check-label mb-0 fw-bold d-block" for="address-2">Robert Bruce<span
                                            class="radio-select-content"><span>3448 Ile De France St #242<br>Fort
                                                Wainwright, <br>Alaska, 99703<span class="d-block mb-0 pt-2">+(901)
                                                    637-734</span></span></span></label><a class="fs--1"
                                        href="#!">Edit</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Payment Method</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-check mb-0"><input class="form-check-input" type="radio" value=""
                                id="credit-card" checked="checked" name="payment-method"><label
                                class="form-check-label mb-2 fs-1" for="credit-card">Credit Card</label></div>
                        <div class="row gx-0 ps-2 mb-4">
                            <div class="col-sm-8 px-3">
                                <div class="mb-3"><label class="form-label ls text-uppercase text-600 fw-semi-bold mb-0"
                                        for="inputNumber">Card Number</label><input class="form-control"
                                        id="inputNumber" type="text" placeholder="•••• •••• •••• ••••"></div>
                                <div class="row align-items-center">
                                    <div class="col-6"><label
                                            class="form-label ls text-uppercase text-600 fw-semi-bold mb-0">Exp
                                            Date</label><input class="form-control" type="text" placeholder="mm/yyyy">
                                    </div>
                                    <div class="col-6"><label
                                            class="form-label ls text-uppercase text-600 fw-semi-bold mb-0">CVV<a
                                                class="d-inline-block" href="#" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title=""
                                                data-bs-original-title="Card verification value"
                                                aria-label="Card verification value"><svg
                                                    class="svg-inline--fa fa-question-circle fa-w-16 ms-2"
                                                    aria-hidden="true" focusable="false" data-prefix="fa"
                                                    data-icon="question-circle" role="img"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                    data-fa-i2svg="">
                                                    <path fill="currentColor"
                                                        d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z">
                                                    </path>
                                                </svg>
                                                <!-- <span class="fa fa-question-circle ms-2"></span> Font Awesome fontawesome.com --></a></label><input
                                            class="form-control" type="text" placeholder="123" maxlength="3"
                                            pattern="[0-9]{3}"></div>
                                </div>
                            </div>
                            <div class="col-4 ps-3 text-center pt-2 d-none d-sm-block">
                                <div class="rounded-1 p-2 mt-3 bg-100">
                                    <div class="text-uppercase fs--2 fw-bold">We Accept</div><img
                                        src="../../assets/img/icons/icon-payment-methods-grid.png" alt="" width="120">
                                </div>
                            </div>
                        </div>
                        <div class="form-check d-flex align-items-center"><input class="form-check-input" type="radio"
                                value="" id="paypal" name="payment-method"><label class="form-check-label mb-0 ms-2"
                                for="paypal"><img src="../../assets/img/icons/icon-paypal-full.png" height="20"
                                    alt=""></label></div>
                        <div class="border-dashed-bottom my-5"></div>
                        <div class="row">
                            <div class="col-md-7 col-xl-12 col-xxl-7 px-md-3 mb-xxl-0 position-relative">
                                <div class="d-flex"><img class="me-3" src="../../assets/img/icons/shield.png" alt=""
                                        width="60" height="60">
                                    <div class="flex-1">
                                        <h5 class="mb-2">Buyer Protection</h5>
                                        <div class="form-check mb-0"><input class="form-check-input"
                                                id="protection-option-1" type="checkbox" checked="checked"><label
                                                class="form-check-label mb-0" for="protection-option-1"> <strong>Full
                                                    Refund </strong>If you don't <br
                                                    class="d-none d-md-block d-lg-none">receive your order</label></div>
                                        <div class="form-check"><input class="form-check-input" id="protection-option-2"
                                                type="checkbox" checked="checked"><label class="form-check-label mb-0"
                                                for="protection-option-2"> <strong>Full or Partial Refund, </strong>If
                                                the product is not as described in details</label></div><a
                                            class="fs--1 ms-3 ps-2" href="#!">Learn More<svg
                                                class="svg-inline--fa fa-caret-right fa-w-6 ms-1"
                                                data-fa-transform="down-2" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="caret-right" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"
                                                data-fa-i2svg="" style="transform-origin: 0.1875em 0.625em;">
                                                <g transform="translate(96 256)">
                                                    <g transform="translate(0, 64)  scale(1, 1)  rotate(0 0 0)">
                                                        <path fill="currentColor"
                                                            d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"
                                                            transform="translate(-96 -256)"></path>
                                                    </g>
                                                </g>
                                            </svg>
                                            <!-- <span class="fas fa-caret-right ms-1" data-fa-transform="down-2">    </span> Font Awesome fontawesome.com --></a>
                                    </div>
                                </div>
                                <div class="vertical-line d-none d-md-block d-xl-none d-xxl-block"> </div>
                            </div>
                            <div
                                class="col-md-5 col-xl-12 col-xxl-5 ps-lg-4 ps-xl-2 ps-xxl-5 text-center text-md-start text-xl-center text-xxl-start">
                                <div class="border-dashed-bottom d-block d-md-none d-xl-block d-xxl-none my-4"></div>
                                <div class="fs-2 fw-semi-bold">All Total: <span class="text-primary">$3320</span></div>
                                <button class="btn btn-success mt-3 px-5" type="submit">Confirm &amp; Pay</button>
                                <p class="fs--1 mt-3 mb-0">By clicking <strong>Confirm &amp; Pay </strong>button you
                                    agree to the <a href="#!">Terms &amp; Conditions</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>


@endsection
