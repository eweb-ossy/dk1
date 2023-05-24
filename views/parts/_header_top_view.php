<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ヘッダー上部バー　表示用
?>
<div class="header-top">
    <div class="header-top-l">
        <!-- ヘッダーロゴ -->
        <div class="header-top-logo">
            <?php if ($logo_uri_header): ?>
                <img height="38" src="<?= $logo_uri_header ?>" alt="">
            <?php else: ?>
            <svg x="0px" y="0px" width="146px" height="36px" viewBox="0 0 146 36">
                <path fill="#fff" d="M144,23.3c0.2-3.9-1-7.7-3.4-10.9c-2.7-3.5-6.6-5.8-11-6.4c-5-0.7-10,1-13.7,4.5c-0.2,0.2-0.2,0.6,0,0.8 c0.2,0.2,0.6,0.2,0.8,0c3.4-3.3,8.1-4.8,12.8-4.2c4.1,0.5,7.8,2.7,10.3,5.9c2.2,2.9,3.3,6.5,3.2,10.1c-0.6,0.2-1.1,0.8-1.1,1.4 c0,0.8,0.7,1.5,1.5,1.5c0.8,0,1.5-0.7,1.5-1.5C144.9,24,144.5,23.5,144,23.3z"/>
                <path fill="#fff" d="M4.3,13.5c0-1,0-1.4-0.1-2.1h3.3c-0.1,0.6-0.1,1-0.1,2.1v1.4H9v-2.5c0.5,0.1,1.4,0.1,2.6,0.1h6 c1.1,0,2,0,2.5-0.1v3.1c-0.7-0.1-1.6-0.1-2.6-0.1h-0.1v11.9c0,1.2-0.3,1.9-1,2.3c-0.5,0.3-1.6,0.5-3.2,0.5c-0.6,0-1.4,0-2.4-0.1 c-0.1-1.3-0.2-2.1-0.6-3.1c1.6,0.2,2.4,0.2,3,0.2c0.8,0,1-0.1,1-0.5V15.3h-2.8c-0.5,0-1.1,0-1.5,0v2.4c-0.5,0-1-0.1-1.7-0.1H7.4 V20c1.2-0.2,1.6-0.3,2.6-0.6v2.8c-1.3,0.3-1.8,0.4-2.6,0.6v4.8c0,1-0.3,1.7-0.9,2C6,30,4.9,30.1,3.7,30.1c-0.4,0-1,0-1.6-0.1 c-0.1-1.3-0.3-1.9-0.7-3c0.9,0.1,1.6,0.2,2.1,0.2c0.5,0,0.7-0.2,0.7-0.6v-3.2c-1.6,0.3-2.2,0.4-2.5,0.4l-0.4-3.1 c0.3,0,0.3,0,0.4,0c0.5,0,1.3-0.1,2.6-0.2v-2.8h-1c-0.7,0-1.2,0-1.7,0.1v-3c0.5,0.1,0.9,0.1,1.5,0.1h1.2V13.5z"/>
                <path fill="#fff" d="M31.6,18c-0.1,0.1-0.1,0.2-0.3,0.4c-1,1.3-2,2.5-3,3.5c-1.8,1.7-3.3,2.8-5.4,3.8c-0.6-1.1-1-1.7-1.9-2.5 c1.7-0.7,2.4-1.1,3.6-1.9c-0.9-0.9-1.7-1.6-3.2-2.6l1.9-2.2c0.5,0.3,0.6,0.5,1,0.7c0.4-0.5,0.5-0.8,0.8-1.3h-1.9 c-0.8,0-1.2,0-1.7,0.1V13c0.7,0.1,1.1,0.1,2.1,0.1h2.1v-0.3c0-0.6,0-1-0.1-1.4h3.2c-0.1,0.4-0.1,0.8-0.1,1.4v0.3h1.1 c1.1,0,1.6,0,2.3-0.1v2.9c-0.6-0.1-1.1-0.1-1.9-0.1h-1.9c-0.6,1.1-1.3,2.1-2,2.9c0.2,0.2,0.2,0.2,0.6,0.5c0.7-0.7,1.5-1.6,1.8-2.2 c0.1-0.1,0.2-0.4,0.4-0.8l2.6,1.5L31.6,18z M32.7,22.4c-0.4,0.5-0.4,0.5-1.3,1.5c-0.4,0.4-0.7,0.8-1.3,1.4c0.9,0.9,1.9,1.6,3,2.2 c-0.9,0.9-1.3,1.6-1.9,2.6c-1.4-1-2.3-1.7-3.3-2.8c-1.6,1.3-2.9,2.2-4.7,3.1c-0.7-1.1-1.1-1.7-2.1-2.5c3.9-1.6,6.9-3.9,9.3-7.4 l2.2,1.6v-7c0-0.9,0-1.4-0.1-2h3c-0.1,0.6-0.1,1-0.1,2v8.3c0,1.1,0,1.5,0.1,2h-3c0.1-0.5,0.1-1,0.1-2V22.4z M39.7,11.6 c-0.1,0.6-0.1,1.1-0.1,2.4v13.3c0,2.3-0.7,2.9-3.5,2.9c-0.4,0-0.8,0-2-0.1c-0.1-1.1-0.3-1.8-0.8-2.9c1.1,0.1,1.7,0.2,2.4,0.2 c0.7,0,0.9-0.1,0.9-0.7V14.1c0-1.3,0-1.8-0.1-2.5H39.7z"/>
                <path fill="#fff" d="M51.7,10.6c-0.1,1-0.2,2-0.2,3.5v6.6l2.6-2.6c1.1-1.1,1.6-1.7,2-2.4h6.2c-0.9,0.8-1.2,1.1-2.5,2.3l-3.4,3.3 l3.9,5.7c1.4,2.1,1.5,2.2,2.2,3h-6c-0.4-1-0.9-2-1.5-3.1l-1.8-2.9l-1.8,1.7v0.9c0,1.9,0,2.5,0.2,3.4h-4.8c0.1-1.1,0.2-2,0.2-3.5 V14.1c0-1.6-0.1-2.7-0.2-3.5H51.7z"/>
                <path fill="#fff" d="M65.5,24.3c0.2,1.9,1.3,2.9,3.2,2.9c0.9,0,1.8-0.3,2.4-0.9c0.4-0.3,0.5-0.6,0.7-1.2l4.1,1.2 c-0.5,1.2-0.9,1.8-1.5,2.4c-1.3,1.3-3.2,2-5.5,2c-2.3,0-4.1-0.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7c0-4.6,3-7.7,7.5-7.7 c3.7,0,6.2,2,7,5.5c0.2,0.8,0.3,1.7,0.4,3.1c0,0.1,0,0.2,0,0.5H65.5z M71.6,21c-0.3-1.5-1.3-2.3-3-2.3c-1.7,0-2.7,0.8-3.1,2.3 H71.6z"/>
                <path fill="#fff" d="M80.6,24.3c0.2,1.9,1.3,2.9,3.2,2.9c0.9,0,1.8-0.3,2.4-0.9c0.4-0.3,0.5-0.6,0.7-1.2l4.1,1.2 c-0.5,1.2-0.9,1.8-1.5,2.4c-1.3,1.3-3.2,2-5.5,2c-2.3,0-4.1-0.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7c0-4.6,3-7.7,7.5-7.7 c3.7,0,6.2,2,7,5.5c0.2,0.8,0.3,1.7,0.4,3.1c0,0.1,0,0.2,0,0.5H80.6z M86.7,21c-0.3-1.5-1.3-2.3-3-2.3c-1.7,0-2.7,0.8-3.1,2.3 H86.7z"/>
                <path fill="#fff" d="M91.3,34.4c0.1-1,0.2-2.2,0.2-3.4V19.5c0-1.5,0-2.5-0.2-3.7h4.6v0.8c0,0.1,0,0.3,0,0.4 c1.4-1.2,2.9-1.7,4.8-1.7c2.1,0,3.8,0.6,5,1.8c1.2,1.2,1.9,3.1,1.9,5.5c0,2.4-0.7,4.3-2,5.7c-1.2,1.2-2.9,1.9-4.8,1.9 c-1.1,0-2.3-0.2-3.2-0.6c-0.6-0.3-1-0.5-1.6-1.1c0,0.2,0,0.5,0,0.8V31c0,1.3,0,2.4,0.2,3.4H91.3z M102,25.8c0.7-0.6,1-1.7,1-3.1 c0-2.4-1.3-3.8-3.5-3.8c-1.9,0-3.5,1.7-3.5,3.9c0,2.2,1.6,3.9,3.7,3.9C100.6,26.6,101.4,26.3,102,25.8z"/>
                <path fill="#fff" d="M112.3,24.3c0.2,1.9,1.3,2.9,3.2,2.9c0.9,0,1.8-0.3,2.4-0.9c0.4-0.3,0.5-0.6,0.7-1.2l4.1,1.2 c-0.5,1.2-0.9,1.8-1.5,2.4c-1.3,1.3-3.2,2-5.5,2c-2.3,0-4.1-0.7-5.4-2c-1.4-1.4-2.1-3.4-2.1-5.7c0-4.6,3-7.7,7.5-7.7 c3.7,0,6.2,2,7,5.5c0.2,0.8,0.3,1.7,0.4,3.1c0,0.1,0,0.2,0,0.5H112.3z M118.4,21c-0.3-1.5-1.3-2.3-3-2.3c-1.7,0-2.7,0.8-3.1,2.3 H118.4z"/>
                <polygon fill="#1B97A8" points="124.5,19.1 131.3,19.1 127.9,32.9 "/>
                <path fill="#fff" d="M131,19.4l-1.5,6.2l-1.5,6.2l-1.5-6.2l-1.5-6.2h3H131z M131.7,18.8H131h-3h-3h-0.7l0.2,0.7l1.5,6.2l1.5,6.2 l0.5,2.2l0.5-2.2l1.5-6.2l1.5-6.2L131.7,18.8z M131,20L131,20L131,20z"/>
                <polygon fill="#1B97A8" points="125.1,19.4 144.1,2.2 129.1,23 "/>
                <path fill="#fff" d="M143.2,3.2l-6.4,8.9l-7.8,10.8l-1.9-1.7l-1.9-1.7l9.8-8.9L143.2,3.2z M145,1.2l-10.1,9.1l-10.1,9.1l2.1,1.9 l2.1,1.9l7.9-11L145,1.2z"/>
                <path fill="#fff" d="M131.6,20.4c0,2-1.7,3.7-3.7,3.7c-2,0-3.7-1.7-3.7-3.7c0-2,1.7-3.7,3.7-3.7C130,16.6,131.6,18.3,131.6,20.4"/>
                <path fill="#221815" d="M127.9,24.6c-2.4,0-4.3-1.9-4.3-4.3s1.9-4.3,4.3-4.3c2.4,0,4.3,1.9,4.3,4.3S130.3,24.6,127.9,24.6z M127.9,17.2 c-1.7,0-3.2,1.4-3.2,3.2c0,1.7,1.4,3.2,3.2,3.2c1.7,0,3.2-1.4,3.2-3.2C131.1,18.6,129.7,17.2,127.9,17.2z"/>
                <path fill="#221815" d="M130.4,20.4c0,1.4-1.1,2.5-2.5,2.5c-1.4,0-2.5-1.1-2.5-2.5c0-1.4,1.1-2.5,2.5-2.5 C129.3,17.9,130.4,19,130.4,20.4"/>
            </svg>
            <?php endif; ?>
        </div>
        <div class="header-top-text-l">
            <?php if (isset($area_name)) {
                echo $area_name;
            } else {
                echo $login_name;
            } ?>
        </div>
    </div>
    <div class="header-top-r">
        <!-- 会社名表示 -->
        <div class="header-top-text-r"><?= $company_name->value ?></div>
        <!-- logout -->
        <div class="header-top-btn-r"><a href="logout"></a></div>
    </div>
</div>