<?php
?>
<!-- Toast Container -->
<div id="toast"
     class="toast d-none position-fixed top-50 start-50 translate-middle fade show"
     role="alert"
     aria-live="assertive"
     aria-atomic="true"
     style="z-index: 1055; min-width: 300px; height: 80px; margin-top: 20px"
>
    <div class="toast-body position-relative w-100 d-flex flex-row justify-content-center align-items-center text-center" style="height: 100%">
        <span id="toast-message" style="font-size: 20px; color: white">Sample Response</span>
<!--        <div class="toast-icon mt-2" id="toast-close" style="cursor: pointer;">-->
<!--            <i class="fa-regular fa-circle-xmark"></i>-->
<!--        </div>-->
    </div>
</div>

<!-- Toast Container -->
<div class="position-fixed top-50 start-50 translate-middle fade show" style="z-index: 1100">
    <div id="shareToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="shareToastMessage">
                <!-- Message will be inserted here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>



