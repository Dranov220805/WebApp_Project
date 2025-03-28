<?php

?>
<!--<p id="para"></p>-->
<!--<h2>Hi world oke</h2>-->
<!---->
<!--<button type="button" id="button">Submit</button>-->
<!--<a href="/location/LOC0000007">Update time</a>-->

<!doctype html>
<html lang="en">
<?php
    include "./views/layout/partials/header.php";
?>
<body>
    <section class="home-section-1" id="home-section-1">
        <div class="home-section-1__logo"></div>
        <div class="home-section-1__bg"></div>
        <div class="home-section-1__text">
            <span class="home-section-1__text--sub-title">Teambuilding</span>
            <h5 class="home-section-1__text--title">Chào mừng bạn đến với Team Buiding CLB IT-Zone!<img src="./public/img/icon/hand-icon.png"
                                                                                                         alt=""></h5>
            <span class="home-section-1__text--para">Hãy cùng tham gia và trải nghiệm TeamBuilding cùng IT-Zone để phát triển bản thân và khám phá những tiềm năng mới trong chính mình!</span>
        </div>
        <a href="/log/login" class="home-section-1__btn itz-btn-home btn">
            <span class="text-light loading-item">Tham gia ngay</span>
        </a>
    </section>

    <section class="home-section-3" id="home-section-3">
        <span class="itz-sub-title home-section-3__sub-title">Hoạt động</span>
        <h3 class="home-section-3__title">Team building</h3>
        <div class="home-section-3__content d-flex flex-wrap">
            <div class="home-section-3__content--left col-lg-6 col-md-6 col-sm-6 d-flex flex-wrap">
                <span>
                    Hoạt động team building nằm trong vòng 2 trong kỳ tuyển thành viên hằng năm của câu lạc bộ. Đây không chỉ là một sự kiện thường niên mà còn là một phần quan trọng trong việc phát triển và kết nối các thành viên của câu lạc bộ. Các bạn sẽ được trải nghiệm, học hỏi, và tạo dựng những mối quan hệ trong môi trường thân thiện.
                </span>
            </div>

            <div class="home-section-3__content--right col-lg-6 col-md-6 col-sm-6 d-flex flex-wrap">
                <div class="col-lg-12 col-md-12 col-sm-12 home-section-3__content--inner">
                    <img class="w-100 home-section-3__content--img" src="https://placehold.co/600x400" alt="">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 home-section-3__content--inner">
                    <img class="w-100 home-section-3__content--img" src="https://placehold.co/600x400" alt="">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 home-section-3__content--inner">
                    <img class="w-100 home-section-3__content--img" src="https://placehold.co/600x400" alt="">
                </div>
            </div>
        </div>
    </section>

    <section class="home-section-4" id="home-section-4">
        <h3 class="home-section-4__title">Bạn đã sẵn sàng chưa?</h3>
        <a href="/log/login" class="btn itz-btn-home home-section-4__btn text-light">
            <span class="text-light loading-item">Tham gia ngay</span>
        </a>
    </section>
</body>
</html>
<?php
    include "./views/layout/partials/overlay_loading.php";
?>