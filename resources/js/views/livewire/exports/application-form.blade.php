<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $applicationinfo->lastname . ', ' . $applicationinfo->firstname }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->

    <style>
        /*!
        *  Font Awesome 4.7.0 by @davegandy - http://fontawesome.io - @fontawesome
        *  License - http://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)
        */

        /* FONT PATH
        * -------------------------- */

        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        hr {
            border: 0.5px solid gray;
        }

        /* Watermark styles */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            pointer-events: none;
            opacity: 0.2;
            /* Adjust the opacity as needed */
        }

        .watermark-text {
            font-size: 128px;
            /* Adjust the font size as needed */
            font-weight: bold;
            color: #ff0000;
            /* Adjust the color as needed */
            transform: rotate(-45deg);
            /* Adjust the rotation as needed */
        }

        @font-face {
            font-family: 'FontAwesome';

            src: url('../fonts/fontawesome-webfont.eot?v=4.7.0');

            src: url('../fonts/fontawesome-webfont.eot?#iefix&v=4.7.0') format('embedded-opentype'), url('../fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'), url('../fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'), url('../fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'), url('../fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg');

            font-weight: normal;

            font-style: normal;
        }

        .fa {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* makes the font 33% larger relative to the icon container */

        .fa-lg {
            font-size: 1.33333333em;
            line-height: 0.75em;
            vertical-align: -15%;
        }

        .fa-2x {
            font-size: 2em;
        }

        .fa-3x {
            font-size: 3em;
        }

        .fa-4x {
            font-size: 4em;
        }

        .fa-5x {
            font-size: 5em;
        }

        .fa-fw {
            width: 1.28571429em;
            text-align: center;
        }

        .fa-ul {
            padding-left: 0;
            margin-left: 2.14285714em;
            list-style-type: none;
        }

        .fa-ul>li {
            position: relative;
        }

        .fa-li {
            position: absolute;
            left: -2.14285714em;
            width: 2.14285714em;
            top: 0.14285714em;
            text-align: center;
        }

        .fa-li.fa-lg {
            left: -1.85714286em;
        }

        .fa-border {
            padding: .2em .25em .15em;
            border: solid 0.08em #eeeeee;
            border-radius: .1em;
        }

        .fa-pull-left {
            float: left;
        }

        .fa-pull-right {
            float: right;
        }

        .fa.fa-pull-left {
            margin-right: .3em;
        }

        .fa.fa-pull-right {
            margin-left: .3em;
        }

        /* Deprecated as of 4.4.0 */

        .pull-right {
            float: right;
        }

        .pull-left {
            float: left;
        }

        .fa.pull-left {
            margin-right: .3em;
        }

        .fa.pull-right {
            margin-left: .3em;
        }

        .fa-spin {
            animation: fa-spin 2s infinite linear;
        }

        .fa-pulse {
            animation: fa-spin 1s infinite steps(8);
        }

        @keyframes fa-spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(359deg);
            }
        }

        .fa-rotate-90 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=1)";
            transform: rotate(90deg);
        }

        .fa-rotate-180 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2)";
            transform: rotate(180deg);
        }

        .fa-rotate-270 {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
            transform: rotate(270deg);
        }

        .fa-flip-horizontal {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1)";
            transform: scale(-1, 1);
        }

        .fa-flip-vertical {
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1)";
            transform: scale(1, -1);
        }

        :root .fa-rotate-90,
        :root .fa-rotate-180,
        :root .fa-rotate-270,
        :root .fa-flip-horizontal,
        :root .fa-flip-vertical {
            filter: none;
        }

        .fa-stack {
            position: relative;
            display: inline-block;
            width: 2em;
            height: 2em;
            line-height: 2em;
            vertical-align: middle;
        }

        .fa-stack-1x,
        .fa-stack-2x {
            position: absolute;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .fa-stack-1x {
            line-height: inherit;
        }

        .fa-stack-2x {
            font-size: 2em;
        }

        .fa-inverse {
            color: #ffffff;
        }

        /* Font Awesome uses the Unicode Private Use Area (PUA) to ensure screen
        readers do not read off random characters that represent icons */

        .fa-glass:before {
            content: "\f000";
        }

        .fa-music:before {
            content: "\f001";
        }

        .fa-search:before {
            content: "\f002";
        }

        .fa-envelope-o:before {
            content: "\f003";
        }

        .fa-heart:before {
            content: "\f004";
        }

        .fa-star:before {
            content: "\f005";
        }

        .fa-star-o:before {
            content: "\f006";
        }

        .fa-user:before {
            content: "\f007";
        }

        .fa-film:before {
            content: "\f008";
        }

        .fa-th-large:before {
            content: "\f009";
        }

        .fa-th:before {
            content: "\f00a";
        }

        .fa-th-list:before {
            content: "\f00b";
        }

        .fa-check:before {
            content: "\f00c";
        }

        .fa-remove:before,
        .fa-close:before,
        .fa-times:before {
            content: "\f00d";
        }

        .fa-search-plus:before {
            content: "\f00e";
        }

        .fa-search-minus:before {
            content: "\f010";
        }

        .fa-power-off:before {
            content: "\f011";
        }

        .fa-signal:before {
            content: "\f012";
        }

        .fa-gear:before,
        .fa-cog:before {
            content: "\f013";
        }

        .fa-trash-o:before {
            content: "\f014";
        }

        .fa-home:before {
            content: "\f015";
        }

        .fa-file-o:before {
            content: "\f016";
        }

        .fa-clock-o:before {
            content: "\f017";
        }

        .fa-road:before {
            content: "\f018";
        }

        .fa-download:before {
            content: "\f019";
        }

        .fa-arrow-circle-o-down:before {
            content: "\f01a";
        }

        .fa-arrow-circle-o-up:before {
            content: "\f01b";
        }

        .fa-inbox:before {
            content: "\f01c";
        }

        .fa-play-circle-o:before {
            content: "\f01d";
        }

        .fa-rotate-right:before,
        .fa-repeat:before {
            content: "\f01e";
        }

        .fa-refresh:before {
            content: "\f021";
        }

        .fa-list-alt:before {
            content: "\f022";
        }

        .fa-lock:before {
            content: "\f023";
        }

        .fa-flag:before {
            content: "\f024";
        }

        .fa-headphones:before {
            content: "\f025";
        }

        .fa-volume-off:before {
            content: "\f026";
        }

        .fa-volume-down:before {
            content: "\f027";
        }

        .fa-volume-up:before {
            content: "\f028";
        }

        .fa-qrcode:before {
            content: "\f029";
        }

        .fa-barcode:before {
            content: "\f02a";
        }

        .fa-tag:before {
            content: "\f02b";
        }

        .fa-tags:before {
            content: "\f02c";
        }

        .fa-book:before {
            content: "\f02d";
        }

        .fa-bookmark:before {
            content: "\f02e";
        }

        .fa-print:before {
            content: "\f02f";
        }

        .fa-camera:before {
            content: "\f030";
        }

        .fa-font:before {
            content: "\f031";
        }

        .fa-bold:before {
            content: "\f032";
        }

        .fa-italic:before {
            content: "\f033";
        }

        .fa-text-height:before {
            content: "\f034";
        }

        .fa-text-width:before {
            content: "\f035";
        }

        .fa-align-left:before {
            content: "\f036";
        }

        .fa-align-center:before {
            content: "\f037";
        }

        .fa-align-right:before {
            content: "\f038";
        }

        .fa-align-justify:before {
            content: "\f039";
        }

        .fa-list:before {
            content: "\f03a";
        }

        .fa-dedent:before,
        .fa-outdent:before {
            content: "\f03b";
        }

        .fa-indent:before {
            content: "\f03c";
        }

        .fa-video-camera:before {
            content: "\f03d";
        }

        .fa-photo:before,
        .fa-image:before,
        .fa-picture-o:before {
            content: "\f03e";
        }

        .fa-pencil:before {
            content: "\f040";
        }

        .fa-map-marker:before {
            content: "\f041";
        }

        .fa-adjust:before {
            content: "\f042";
        }

        .fa-tint:before {
            content: "\f043";
        }

        .fa-edit:before,
        .fa-pencil-square-o:before {
            content: "\f044";
        }

        .fa-share-square-o:before {
            content: "\f045";
        }

        .fa-check-square-o:before {
            content: "\f046";
        }

        .fa-arrows:before {
            content: "\f047";
        }

        .fa-step-backward:before {
            content: "\f048";
        }

        .fa-fast-backward:before {
            content: "\f049";
        }

        .fa-backward:before {
            content: "\f04a";
        }

        .fa-play:before {
            content: "\f04b";
        }

        .fa-pause:before {
            content: "\f04c";
        }

        .fa-stop:before {
            content: "\f04d";
        }

        .fa-forward:before {
            content: "\f04e";
        }

        .fa-fast-forward:before {
            content: "\f050";
        }

        .fa-step-forward:before {
            content: "\f051";
        }

        .fa-eject:before {
            content: "\f052";
        }

        .fa-chevron-left:before {
            content: "\f053";
        }

        .fa-chevron-right:before {
            content: "\f054";
        }

        .fa-plus-circle:before {
            content: "\f055";
        }

        .fa-minus-circle:before {
            content: "\f056";
        }

        .fa-times-circle:before {
            content: "\f057";
        }

        .fa-check-circle:before {
            content: "\f058";
        }

        .fa-question-circle:before {
            content: "\f059";
        }

        .fa-info-circle:before {
            content: "\f05a";
        }

        .fa-crosshairs:before {
            content: "\f05b";
        }

        .fa-times-circle-o:before {
            content: "\f05c";
        }

        .fa-check-circle-o:before {
            content: "\f05d";
        }

        .fa-ban:before {
            content: "\f05e";
        }

        .fa-arrow-left:before {
            content: "\f060";
        }

        .fa-arrow-right:before {
            content: "\f061";
        }

        .fa-arrow-up:before {
            content: "\f062";
        }

        .fa-arrow-down:before {
            content: "\f063";
        }

        .fa-mail-forward:before,
        .fa-share:before {
            content: "\f064";
        }

        .fa-expand:before {
            content: "\f065";
        }

        .fa-compress:before {
            content: "\f066";
        }

        .fa-plus:before {
            content: "\f067";
        }

        .fa-minus:before {
            content: "\f068";
        }

        .fa-asterisk:before {
            content: "\f069";
        }

        .fa-exclamation-circle:before {
            content: "\f06a";
        }

        .fa-gift:before {
            content: "\f06b";
        }

        .fa-leaf:before {
            content: "\f06c";
        }

        .fa-fire:before {
            content: "\f06d";
        }

        .fa-eye:before {
            content: "\f06e";
        }

        .fa-eye-slash:before {
            content: "\f070";
        }

        .fa-warning:before,
        .fa-exclamation-triangle:before {
            content: "\f071";
        }

        .fa-plane:before {
            content: "\f072";
        }

        .fa-calendar:before {
            content: "\f073";
        }

        .fa-random:before {
            content: "\f074";
        }

        .fa-comment:before {
            content: "\f075";
        }

        .fa-magnet:before {
            content: "\f076";
        }

        .fa-chevron-up:before {
            content: "\f077";
        }

        .fa-chevron-down:before {
            content: "\f078";
        }

        .fa-retweet:before {
            content: "\f079";
        }

        .fa-shopping-cart:before {
            content: "\f07a";
        }

        .fa-folder:before {
            content: "\f07b";
        }

        .fa-folder-open:before {
            content: "\f07c";
        }

        .fa-arrows-v:before {
            content: "\f07d";
        }

        .fa-arrows-h:before {
            content: "\f07e";
        }

        .fa-bar-chart-o:before,
        .fa-bar-chart:before {
            content: "\f080";
        }

        .fa-twitter-square:before {
            content: "\f081";
        }

        .fa-facebook-square:before {
            content: "\f082";
        }

        .fa-camera-retro:before {
            content: "\f083";
        }

        .fa-key:before {
            content: "\f084";
        }

        .fa-gears:before,
        .fa-cogs:before {
            content: "\f085";
        }

        .fa-comments:before {
            content: "\f086";
        }

        .fa-thumbs-o-up:before {
            content: "\f087";
        }

        .fa-thumbs-o-down:before {
            content: "\f088";
        }

        .fa-star-half:before {
            content: "\f089";
        }

        .fa-heart-o:before {
            content: "\f08a";
        }

        .fa-sign-out:before {
            content: "\f08b";
        }

        .fa-linkedin-square:before {
            content: "\f08c";
        }

        .fa-thumb-tack:before {
            content: "\f08d";
        }

        .fa-external-link:before {
            content: "\f08e";
        }

        .fa-sign-in:before {
            content: "\f090";
        }

        .fa-trophy:before {
            content: "\f091";
        }

        .fa-github-square:before {
            content: "\f092";
        }

        .fa-upload:before {
            content: "\f093";
        }

        .fa-lemon-o:before {
            content: "\f094";
        }

        .fa-phone:before {
            content: "\f095";
        }

        .fa-square-o:before {
            content: "\f096";
        }

        .fa-bookmark-o:before {
            content: "\f097";
        }

        .fa-phone-square:before {
            content: "\f098";
        }

        .fa-twitter:before {
            content: "\f099";
        }

        .fa-facebook-f:before,
        .fa-facebook:before {
            content: "\f09a";
        }

        .fa-github:before {
            content: "\f09b";
        }

        .fa-unlock:before {
            content: "\f09c";
        }

        .fa-credit-card:before {
            content: "\f09d";
        }

        .fa-feed:before,
        .fa-rss:before {
            content: "\f09e";
        }

        .fa-hdd-o:before {
            content: "\f0a0";
        }

        .fa-bullhorn:before {
            content: "\f0a1";
        }

        .fa-bell:before {
            content: "\f0f3";
        }

        .fa-certificate:before {
            content: "\f0a3";
        }

        .fa-hand-o-right:before {
            content: "\f0a4";
        }

        .fa-hand-o-left:before {
            content: "\f0a5";
        }

        .fa-hand-o-up:before {
            content: "\f0a6";
        }

        .fa-hand-o-down:before {
            content: "\f0a7";
        }

        .fa-arrow-circle-left:before {
            content: "\f0a8";
        }

        .fa-arrow-circle-right:before {
            content: "\f0a9";
        }

        .fa-arrow-circle-up:before {
            content: "\f0aa";
        }

        .fa-arrow-circle-down:before {
            content: "\f0ab";
        }

        .fa-globe:before {
            content: "\f0ac";
        }

        .fa-wrench:before {
            content: "\f0ad";
        }

        .fa-tasks:before {
            content: "\f0ae";
        }

        .fa-filter:before {
            content: "\f0b0";
        }

        .fa-briefcase:before {
            content: "\f0b1";
        }

        .fa-arrows-alt:before {
            content: "\f0b2";
        }

        .fa-group:before,
        .fa-users:before {
            content: "\f0c0";
        }

        .fa-chain:before,
        .fa-link:before {
            content: "\f0c1";
        }

        .fa-cloud:before {
            content: "\f0c2";
        }

        .fa-flask:before {
            content: "\f0c3";
        }

        .fa-cut:before,
        .fa-scissors:before {
            content: "\f0c4";
        }

        .fa-copy:before,
        .fa-files-o:before {
            content: "\f0c5";
        }

        .fa-paperclip:before {
            content: "\f0c6";
        }

        .fa-save:before,
        .fa-floppy-o:before {
            content: "\f0c7";
        }

        .fa-square:before {
            content: "\f0c8";
        }

        .fa-navicon:before,
        .fa-reorder:before,
        .fa-bars:before {
            content: "\f0c9";
        }

        .fa-list-ul:before {
            content: "\f0ca";
        }

        .fa-list-ol:before {
            content: "\f0cb";
        }

        .fa-strikethrough:before {
            content: "\f0cc";
        }

        .fa-underline:before {
            content: "\f0cd";
        }

        .fa-table:before {
            content: "\f0ce";
        }

        .fa-magic:before {
            content: "\f0d0";
        }

        .fa-truck:before {
            content: "\f0d1";
        }

        .fa-pinterest:before {
            content: "\f0d2";
        }

        .fa-pinterest-square:before {
            content: "\f0d3";
        }

        .fa-google-plus-square:before {
            content: "\f0d4";
        }

        .fa-google-plus:before {
            content: "\f0d5";
        }

        .fa-money:before {
            content: "\f0d6";
        }

        .fa-caret-down:before {
            content: "\f0d7";
        }

        .fa-caret-up:before {
            content: "\f0d8";
        }

        .fa-caret-left:before {
            content: "\f0d9";
        }

        .fa-caret-right:before {
            content: "\f0da";
        }

        .fa-columns:before {
            content: "\f0db";
        }

        .fa-unsorted:before,
        .fa-sort:before {
            content: "\f0dc";
        }

        .fa-sort-down:before,
        .fa-sort-desc:before {
            content: "\f0dd";
        }

        .fa-sort-up:before,
        .fa-sort-asc:before {
            content: "\f0de";
        }

        .fa-envelope:before {
            content: "\f0e0";
        }

        .fa-linkedin:before {
            content: "\f0e1";
        }

        .fa-rotate-left:before,
        .fa-undo:before {
            content: "\f0e2";
        }

        .fa-legal:before,
        .fa-gavel:before {
            content: "\f0e3";
        }

        .fa-dashboard:before,
        .fa-tachometer:before {
            content: "\f0e4";
        }

        .fa-comment-o:before {
            content: "\f0e5";
        }

        .fa-comments-o:before {
            content: "\f0e6";
        }

        .fa-flash:before,
        .fa-bolt:before {
            content: "\f0e7";
        }

        .fa-sitemap:before {
            content: "\f0e8";
        }

        .fa-umbrella:before {
            content: "\f0e9";
        }

        .fa-paste:before,
        .fa-clipboard:before {
            content: "\f0ea";
        }

        .fa-lightbulb-o:before {
            content: "\f0eb";
        }

        .fa-exchange:before {
            content: "\f0ec";
        }

        .fa-cloud-download:before {
            content: "\f0ed";
        }

        .fa-cloud-upload:before {
            content: "\f0ee";
        }

        .fa-user-md:before {
            content: "\f0f0";
        }

        .fa-stethoscope:before {
            content: "\f0f1";
        }

        .fa-suitcase:before {
            content: "\f0f2";
        }

        .fa-bell-o:before {
            content: "\f0a2";
        }

        .fa-coffee:before {
            content: "\f0f4";
        }

        .fa-cutlery:before {
            content: "\f0f5";
        }

        .fa-file-text-o:before {
            content: "\f0f6";
        }

        .fa-building-o:before {
            content: "\f0f7";
        }

        .fa-hospital-o:before {
            content: "\f0f8";
        }

        .fa-ambulance:before {
            content: "\f0f9";
        }

        .fa-medkit:before {
            content: "\f0fa";
        }

        .fa-fighter-jet:before {
            content: "\f0fb";
        }

        .fa-beer:before {
            content: "\f0fc";
        }

        .fa-h-square:before {
            content: "\f0fd";
        }

        .fa-plus-square:before {
            content: "\f0fe";
        }

        .fa-angle-double-left:before {
            content: "\f100";
        }

        .fa-angle-double-right:before {
            content: "\f101";
        }

        .fa-angle-double-up:before {
            content: "\f102";
        }

        .fa-angle-double-down:before {
            content: "\f103";
        }

        .fa-angle-left:before {
            content: "\f104";
        }

        .fa-angle-right:before {
            content: "\f105";
        }

        .fa-angle-up:before {
            content: "\f106";
        }

        .fa-angle-down:before {
            content: "\f107";
        }

        .fa-desktop:before {
            content: "\f108";
        }

        .fa-laptop:before {
            content: "\f109";
        }

        .fa-tablet:before {
            content: "\f10a";
        }

        .fa-mobile-phone:before,
        .fa-mobile:before {
            content: "\f10b";
        }

        .fa-circle-o:before {
            content: "\f10c";
        }

        .fa-quote-left:before {
            content: "\f10d";
        }

        .fa-quote-right:before {
            content: "\f10e";
        }

        .fa-spinner:before {
            content: "\f110";
        }

        .fa-circle:before {
            content: "\f111";
        }

        .fa-mail-reply:before,
        .fa-reply:before {
            content: "\f112";
        }

        .fa-github-alt:before {
            content: "\f113";
        }

        .fa-folder-o:before {
            content: "\f114";
        }

        .fa-folder-open-o:before {
            content: "\f115";
        }

        .fa-smile-o:before {
            content: "\f118";
        }

        .fa-frown-o:before {
            content: "\f119";
        }

        .fa-meh-o:before {
            content: "\f11a";
        }

        .fa-gamepad:before {
            content: "\f11b";
        }

        .fa-keyboard-o:before {
            content: "\f11c";
        }

        .fa-flag-o:before {
            content: "\f11d";
        }

        .fa-flag-checkered:before {
            content: "\f11e";
        }

        .fa-terminal:before {
            content: "\f120";
        }

        .fa-code:before {
            content: "\f121";
        }

        .fa-mail-reply-all:before,
        .fa-reply-all:before {
            content: "\f122";
        }

        .fa-star-half-empty:before,
        .fa-star-half-full:before,
        .fa-star-half-o:before {
            content: "\f123";
        }

        .fa-location-arrow:before {
            content: "\f124";
        }

        .fa-crop:before {
            content: "\f125";
        }

        .fa-code-fork:before {
            content: "\f126";
        }

        .fa-unlink:before,
        .fa-chain-broken:before {
            content: "\f127";
        }

        .fa-question:before {
            content: "\f128";
        }

        .fa-info:before {
            content: "\f129";
        }

        .fa-exclamation:before {
            content: "\f12a";
        }

        .fa-superscript:before {
            content: "\f12b";
        }

        .fa-subscript:before {
            content: "\f12c";
        }

        .fa-eraser:before {
            content: "\f12d";
        }

        .fa-puzzle-piece:before {
            content: "\f12e";
        }

        .fa-microphone:before {
            content: "\f130";
        }

        .fa-microphone-slash:before {
            content: "\f131";
        }

        .fa-shield:before {
            content: "\f132";
        }

        .fa-calendar-o:before {
            content: "\f133";
        }

        .fa-fire-extinguisher:before {
            content: "\f134";
        }

        .fa-rocket:before {
            content: "\f135";
        }

        .fa-maxcdn:before {
            content: "\f136";
        }

        .fa-chevron-circle-left:before {
            content: "\f137";
        }

        .fa-chevron-circle-right:before {
            content: "\f138";
        }

        .fa-chevron-circle-up:before {
            content: "\f139";
        }

        .fa-chevron-circle-down:before {
            content: "\f13a";
        }

        .fa-html5:before {
            content: "\f13b";
        }

        .fa-css3:before {
            content: "\f13c";
        }

        .fa-anchor:before {
            content: "\f13d";
        }

        .fa-unlock-alt:before {
            content: "\f13e";
        }

        .fa-bullseye:before {
            content: "\f140";
        }

        .fa-ellipsis-h:before {
            content: "\f141";
        }

        .fa-ellipsis-v:before {
            content: "\f142";
        }

        .fa-rss-square:before {
            content: "\f143";
        }

        .fa-play-circle:before {
            content: "\f144";
        }

        .fa-ticket:before {
            content: "\f145";
        }

        .fa-minus-square:before {
            content: "\f146";
        }

        .fa-minus-square-o:before {
            content: "\f147";
        }

        .fa-level-up:before {
            content: "\f148";
        }

        .fa-level-down:before {
            content: "\f149";
        }

        .fa-check-square:before {
            content: "\f14a";
        }

        .fa-pencil-square:before {
            content: "\f14b";
        }

        .fa-external-link-square:before {
            content: "\f14c";
        }

        .fa-share-square:before {
            content: "\f14d";
        }

        .fa-compass:before {
            content: "\f14e";
        }

        .fa-toggle-down:before,
        .fa-caret-square-o-down:before {
            content: "\f150";
        }

        .fa-toggle-up:before,
        .fa-caret-square-o-up:before {
            content: "\f151";
        }

        .fa-toggle-right:before,
        .fa-caret-square-o-right:before {
            content: "\f152";
        }

        .fa-euro:before,
        .fa-eur:before {
            content: "\f153";
        }

        .fa-gbp:before {
            content: "\f154";
        }

        .fa-dollar:before,
        .fa-usd:before {
            content: "\f155";
        }

        .fa-rupee:before,
        .fa-inr:before {
            content: "\f156";
        }

        .fa-cny:before,
        .fa-rmb:before,
        .fa-yen:before,
        .fa-jpy:before {
            content: "\f157";
        }

        .fa-ruble:before,
        .fa-rouble:before,
        .fa-rub:before {
            content: "\f158";
        }

        .fa-won:before,
        .fa-krw:before {
            content: "\f159";
        }

        .fa-bitcoin:before,
        .fa-btc:before {
            content: "\f15a";
        }

        .fa-file:before {
            content: "\f15b";
        }

        .fa-file-text:before {
            content: "\f15c";
        }

        .fa-sort-alpha-asc:before {
            content: "\f15d";
        }

        .fa-sort-alpha-desc:before {
            content: "\f15e";
        }

        .fa-sort-amount-asc:before {
            content: "\f160";
        }

        .fa-sort-amount-desc:before {
            content: "\f161";
        }

        .fa-sort-numeric-asc:before {
            content: "\f162";
        }

        .fa-sort-numeric-desc:before {
            content: "\f163";
        }

        .fa-thumbs-up:before {
            content: "\f164";
        }

        .fa-thumbs-down:before {
            content: "\f165";
        }

        .fa-youtube-square:before {
            content: "\f166";
        }

        .fa-youtube:before {
            content: "\f167";
        }

        .fa-xing:before {
            content: "\f168";
        }

        .fa-xing-square:before {
            content: "\f169";
        }

        .fa-youtube-play:before {
            content: "\f16a";
        }

        .fa-dropbox:before {
            content: "\f16b";
        }

        .fa-stack-overflow:before {
            content: "\f16c";
        }

        .fa-instagram:before {
            content: "\f16d";
        }

        .fa-flickr:before {
            content: "\f16e";
        }

        .fa-adn:before {
            content: "\f170";
        }

        .fa-bitbucket:before {
            content: "\f171";
        }

        .fa-bitbucket-square:before {
            content: "\f172";
        }

        .fa-tumblr:before {
            content: "\f173";
        }

        .fa-tumblr-square:before {
            content: "\f174";
        }

        .fa-long-arrow-down:before {
            content: "\f175";
        }

        .fa-long-arrow-up:before {
            content: "\f176";
        }

        .fa-long-arrow-left:before {
            content: "\f177";
        }

        .fa-long-arrow-right:before {
            content: "\f178";
        }

        .fa-apple:before {
            content: "\f179";
        }

        .fa-windows:before {
            content: "\f17a";
        }

        .fa-android:before {
            content: "\f17b";
        }

        .fa-linux:before {
            content: "\f17c";
        }

        .fa-dribbble:before {
            content: "\f17d";
        }

        .fa-skype:before {
            content: "\f17e";
        }

        .fa-foursquare:before {
            content: "\f180";
        }

        .fa-trello:before {
            content: "\f181";
        }

        .fa-female:before {
            content: "\f182";
        }

        .fa-male:before {
            content: "\f183";
        }

        .fa-gittip:before,
        .fa-gratipay:before {
            content: "\f184";
        }

        .fa-sun-o:before {
            content: "\f185";
        }

        .fa-moon-o:before {
            content: "\f186";
        }

        .fa-archive:before {
            content: "\f187";
        }

        .fa-bug:before {
            content: "\f188";
        }

        .fa-vk:before {
            content: "\f189";
        }

        .fa-weibo:before {
            content: "\f18a";
        }

        .fa-renren:before {
            content: "\f18b";
        }

        .fa-pagelines:before {
            content: "\f18c";
        }

        .fa-stack-exchange:before {
            content: "\f18d";
        }

        .fa-arrow-circle-o-right:before {
            content: "\f18e";
        }

        .fa-arrow-circle-o-left:before {
            content: "\f190";
        }

        .fa-toggle-left:before,
        .fa-caret-square-o-left:before {
            content: "\f191";
        }

        .fa-dot-circle-o:before {
            content: "\f192";
        }

        .fa-wheelchair:before {
            content: "\f193";
        }

        .fa-vimeo-square:before {
            content: "\f194";
        }

        .fa-turkish-lira:before,
        .fa-try:before {
            content: "\f195";
        }

        .fa-plus-square-o:before {
            content: "\f196";
        }

        .fa-space-shuttle:before {
            content: "\f197";
        }

        .fa-slack:before {
            content: "\f198";
        }

        .fa-envelope-square:before {
            content: "\f199";
        }

        .fa-wordpress:before {
            content: "\f19a";
        }

        .fa-openid:before {
            content: "\f19b";
        }

        .fa-institution:before,
        .fa-bank:before,
        .fa-university:before {
            content: "\f19c";
        }

        .fa-mortar-board:before,
        .fa-graduation-cap:before {
            content: "\f19d";
        }

        .fa-yahoo:before {
            content: "\f19e";
        }

        .fa-google:before {
            content: "\f1a0";
        }

        .fa-reddit:before {
            content: "\f1a1";
        }

        .fa-reddit-square:before {
            content: "\f1a2";
        }

        .fa-stumbleupon-circle:before {
            content: "\f1a3";
        }

        .fa-stumbleupon:before {
            content: "\f1a4";
        }

        .fa-delicious:before {
            content: "\f1a5";
        }

        .fa-digg:before {
            content: "\f1a6";
        }

        .fa-pied-piper-pp:before {
            content: "\f1a7";
        }

        .fa-pied-piper-alt:before {
            content: "\f1a8";
        }

        .fa-drupal:before {
            content: "\f1a9";
        }

        .fa-joomla:before {
            content: "\f1aa";
        }

        .fa-language:before {
            content: "\f1ab";
        }

        .fa-fax:before {
            content: "\f1ac";
        }

        .fa-building:before {
            content: "\f1ad";
        }

        .fa-child:before {
            content: "\f1ae";
        }

        .fa-paw:before {
            content: "\f1b0";
        }

        .fa-spoon:before {
            content: "\f1b1";
        }

        .fa-cube:before {
            content: "\f1b2";
        }

        .fa-cubes:before {
            content: "\f1b3";
        }

        .fa-behance:before {
            content: "\f1b4";
        }

        .fa-behance-square:before {
            content: "\f1b5";
        }

        .fa-steam:before {
            content: "\f1b6";
        }

        .fa-steam-square:before {
            content: "\f1b7";
        }

        .fa-recycle:before {
            content: "\f1b8";
        }

        .fa-automobile:before,
        .fa-car:before {
            content: "\f1b9";
        }

        .fa-cab:before,
        .fa-taxi:before {
            content: "\f1ba";
        }

        .fa-tree:before {
            content: "\f1bb";
        }

        .fa-spotify:before {
            content: "\f1bc";
        }

        .fa-deviantart:before {
            content: "\f1bd";
        }

        .fa-soundcloud:before {
            content: "\f1be";
        }

        .fa-database:before {
            content: "\f1c0";
        }

        .fa-file-pdf-o:before {
            content: "\f1c1";
        }

        .fa-file-word-o:before {
            content: "\f1c2";
        }

        .fa-file-excel-o:before {
            content: "\f1c3";
        }

        .fa-file-powerpoint-o:before {
            content: "\f1c4";
        }

        .fa-file-photo-o:before,
        .fa-file-picture-o:before,
        .fa-file-image-o:before {
            content: "\f1c5";
        }

        .fa-file-zip-o:before,
        .fa-file-archive-o:before {
            content: "\f1c6";
        }

        .fa-file-sound-o:before,
        .fa-file-audio-o:before {
            content: "\f1c7";
        }

        .fa-file-movie-o:before,
        .fa-file-video-o:before {
            content: "\f1c8";
        }

        .fa-file-code-o:before {
            content: "\f1c9";
        }

        .fa-vine:before {
            content: "\f1ca";
        }

        .fa-codepen:before {
            content: "\f1cb";
        }

        .fa-jsfiddle:before {
            content: "\f1cc";
        }

        .fa-life-bouy:before,
        .fa-life-buoy:before,
        .fa-life-saver:before,
        .fa-support:before,
        .fa-life-ring:before {
            content: "\f1cd";
        }

        .fa-circle-o-notch:before {
            content: "\f1ce";
        }

        .fa-ra:before,
        .fa-resistance:before,
        .fa-rebel:before {
            content: "\f1d0";
        }

        .fa-ge:before,
        .fa-empire:before {
            content: "\f1d1";
        }

        .fa-git-square:before {
            content: "\f1d2";
        }

        .fa-git:before {
            content: "\f1d3";
        }

        .fa-y-combinator-square:before,
        .fa-yc-square:before,
        .fa-hacker-news:before {
            content: "\f1d4";
        }

        .fa-tencent-weibo:before {
            content: "\f1d5";
        }

        .fa-qq:before {
            content: "\f1d6";
        }

        .fa-wechat:before,
        .fa-weixin:before {
            content: "\f1d7";
        }

        .fa-send:before,
        .fa-paper-plane:before {
            content: "\f1d8";
        }

        .fa-send-o:before,
        .fa-paper-plane-o:before {
            content: "\f1d9";
        }

        .fa-history:before {
            content: "\f1da";
        }

        .fa-circle-thin:before {
            content: "\f1db";
        }

        .fa-header:before {
            content: "\f1dc";
        }

        .fa-paragraph:before {
            content: "\f1dd";
        }

        .fa-sliders:before {
            content: "\f1de";
        }

        .fa-share-alt:before {
            content: "\f1e0";
        }

        .fa-share-alt-square:before {
            content: "\f1e1";
        }

        .fa-bomb:before {
            content: "\f1e2";
        }

        .fa-soccer-ball-o:before,
        .fa-futbol-o:before {
            content: "\f1e3";
        }

        .fa-tty:before {
            content: "\f1e4";
        }

        .fa-binoculars:before {
            content: "\f1e5";
        }

        .fa-plug:before {
            content: "\f1e6";
        }

        .fa-slideshare:before {
            content: "\f1e7";
        }

        .fa-twitch:before {
            content: "\f1e8";
        }

        .fa-yelp:before {
            content: "\f1e9";
        }

        .fa-newspaper-o:before {
            content: "\f1ea";
        }

        .fa-wifi:before {
            content: "\f1eb";
        }

        .fa-calculator:before {
            content: "\f1ec";
        }

        .fa-paypal:before {
            content: "\f1ed";
        }

        .fa-google-wallet:before {
            content: "\f1ee";
        }

        .fa-cc-visa:before {
            content: "\f1f0";
        }

        .fa-cc-mastercard:before {
            content: "\f1f1";
        }

        .fa-cc-discover:before {
            content: "\f1f2";
        }

        .fa-cc-amex:before {
            content: "\f1f3";
        }

        .fa-cc-paypal:before {
            content: "\f1f4";
        }

        .fa-cc-stripe:before {
            content: "\f1f5";
        }

        .fa-bell-slash:before {
            content: "\f1f6";
        }

        .fa-bell-slash-o:before {
            content: "\f1f7";
        }

        .fa-trash:before {
            content: "\f1f8";
        }

        .fa-copyright:before {
            content: "\f1f9";
        }

        .fa-at:before {
            content: "\f1fa";
        }

        .fa-eyedropper:before {
            content: "\f1fb";
        }

        .fa-paint-brush:before {
            content: "\f1fc";
        }

        .fa-birthday-cake:before {
            content: "\f1fd";
        }

        .fa-area-chart:before {
            content: "\f1fe";
        }

        .fa-pie-chart:before {
            content: "\f200";
        }

        .fa-line-chart:before {
            content: "\f201";
        }

        .fa-lastfm:before {
            content: "\f202";
        }

        .fa-lastfm-square:before {
            content: "\f203";
        }

        .fa-toggle-off:before {
            content: "\f204";
        }

        .fa-toggle-on:before {
            content: "\f205";
        }

        .fa-bicycle:before {
            content: "\f206";
        }

        .fa-bus:before {
            content: "\f207";
        }

        .fa-ioxhost:before {
            content: "\f208";
        }

        .fa-angellist:before {
            content: "\f209";
        }

        .fa-cc:before {
            content: "\f20a";
        }

        .fa-shekel:before,
        .fa-sheqel:before,
        .fa-ils:before {
            content: "\f20b";
        }

        .fa-meanpath:before {
            content: "\f20c";
        }

        .fa-buysellads:before {
            content: "\f20d";
        }

        .fa-connectdevelop:before {
            content: "\f20e";
        }

        .fa-dashcube:before {
            content: "\f210";
        }

        .fa-forumbee:before {
            content: "\f211";
        }

        .fa-leanpub:before {
            content: "\f212";
        }

        .fa-sellsy:before {
            content: "\f213";
        }

        .fa-shirtsinbulk:before {
            content: "\f214";
        }

        .fa-simplybuilt:before {
            content: "\f215";
        }

        .fa-skyatlas:before {
            content: "\f216";
        }

        .fa-cart-plus:before {
            content: "\f217";
        }

        .fa-cart-arrow-down:before {
            content: "\f218";
        }

        .fa-diamond:before {
            content: "\f219";
        }

        .fa-ship:before {
            content: "\f21a";
        }

        .fa-user-secret:before {
            content: "\f21b";
        }

        .fa-motorcycle:before {
            content: "\f21c";
        }

        .fa-street-view:before {
            content: "\f21d";
        }

        .fa-heartbeat:before {
            content: "\f21e";
        }

        .fa-venus:before {
            content: "\f221";
        }

        .fa-mars:before {
            content: "\f222";
        }

        .fa-mercury:before {
            content: "\f223";
        }

        .fa-intersex:before,
        .fa-transgender:before {
            content: "\f224";
        }

        .fa-transgender-alt:before {
            content: "\f225";
        }

        .fa-venus-double:before {
            content: "\f226";
        }

        .fa-mars-double:before {
            content: "\f227";
        }

        .fa-venus-mars:before {
            content: "\f228";
        }

        .fa-mars-stroke:before {
            content: "\f229";
        }

        .fa-mars-stroke-v:before {
            content: "\f22a";
        }

        .fa-mars-stroke-h:before {
            content: "\f22b";
        }

        .fa-neuter:before {
            content: "\f22c";
        }

        .fa-genderless:before {
            content: "\f22d";
        }

        .fa-facebook-official:before {
            content: "\f230";
        }

        .fa-pinterest-p:before {
            content: "\f231";
        }

        .fa-whatsapp:before {
            content: "\f232";
        }

        .fa-server:before {
            content: "\f233";
        }

        .fa-user-plus:before {
            content: "\f234";
        }

        .fa-user-times:before {
            content: "\f235";
        }

        .fa-hotel:before,
        .fa-bed:before {
            content: "\f236";
        }

        .fa-viacoin:before {
            content: "\f237";
        }

        .fa-train:before {
            content: "\f238";
        }

        .fa-subway:before {
            content: "\f239";
        }

        .fa-medium:before {
            content: "\f23a";
        }

        .fa-yc:before,
        .fa-y-combinator:before {
            content: "\f23b";
        }

        .fa-optin-monster:before {
            content: "\f23c";
        }

        .fa-opencart:before {
            content: "\f23d";
        }

        .fa-expeditedssl:before {
            content: "\f23e";
        }

        .fa-battery-4:before,
        .fa-battery:before,
        .fa-battery-full:before {
            content: "\f240";
        }

        .fa-battery-3:before,
        .fa-battery-three-quarters:before {
            content: "\f241";
        }

        .fa-battery-2:before,
        .fa-battery-half:before {
            content: "\f242";
        }

        .fa-battery-1:before,
        .fa-battery-quarter:before {
            content: "\f243";
        }

        .fa-battery-0:before,
        .fa-battery-empty:before {
            content: "\f244";
        }

        .fa-mouse-pointer:before {
            content: "\f245";
        }

        .fa-i-cursor:before {
            content: "\f246";
        }

        .fa-object-group:before {
            content: "\f247";
        }

        .fa-object-ungroup:before {
            content: "\f248";
        }

        .fa-sticky-note:before {
            content: "\f249";
        }

        .fa-sticky-note-o:before {
            content: "\f24a";
        }

        .fa-cc-jcb:before {
            content: "\f24b";
        }

        .fa-cc-diners-club:before {
            content: "\f24c";
        }

        .fa-clone:before {
            content: "\f24d";
        }

        .fa-balance-scale:before {
            content: "\f24e";
        }

        .fa-hourglass-o:before {
            content: "\f250";
        }

        .fa-hourglass-1:before,
        .fa-hourglass-start:before {
            content: "\f251";
        }

        .fa-hourglass-2:before,
        .fa-hourglass-half:before {
            content: "\f252";
        }

        .fa-hourglass-3:before,
        .fa-hourglass-end:before {
            content: "\f253";
        }

        .fa-hourglass:before {
            content: "\f254";
        }

        .fa-hand-grab-o:before,
        .fa-hand-rock-o:before {
            content: "\f255";
        }

        .fa-hand-stop-o:before,
        .fa-hand-paper-o:before {
            content: "\f256";
        }

        .fa-hand-scissors-o:before {
            content: "\f257";
        }

        .fa-hand-lizard-o:before {
            content: "\f258";
        }

        .fa-hand-spock-o:before {
            content: "\f259";
        }

        .fa-hand-pointer-o:before {
            content: "\f25a";
        }

        .fa-hand-peace-o:before {
            content: "\f25b";
        }

        .fa-trademark:before {
            content: "\f25c";
        }

        .fa-registered:before {
            content: "\f25d";
        }

        .fa-creative-commons:before {
            content: "\f25e";
        }

        .fa-gg:before {
            content: "\f260";
        }

        .fa-gg-circle:before {
            content: "\f261";
        }

        .fa-tripadvisor:before {
            content: "\f262";
        }

        .fa-odnoklassniki:before {
            content: "\f263";
        }

        .fa-odnoklassniki-square:before {
            content: "\f264";
        }

        .fa-get-pocket:before {
            content: "\f265";
        }

        .fa-wikipedia-w:before {
            content: "\f266";
        }

        .fa-safari:before {
            content: "\f267";
        }

        .fa-chrome:before {
            content: "\f268";
        }

        .fa-firefox:before {
            content: "\f269";
        }

        .fa-opera:before {
            content: "\f26a";
        }

        .fa-internet-explorer:before {
            content: "\f26b";
        }

        .fa-tv:before,
        .fa-television:before {
            content: "\f26c";
        }

        .fa-contao:before {
            content: "\f26d";
        }

        .fa-500px:before {
            content: "\f26e";
        }

        .fa-amazon:before {
            content: "\f270";
        }

        .fa-calendar-plus-o:before {
            content: "\f271";
        }

        .fa-calendar-minus-o:before {
            content: "\f272";
        }

        .fa-calendar-times-o:before {
            content: "\f273";
        }

        .fa-calendar-check-o:before {
            content: "\f274";
        }

        .fa-industry:before {
            content: "\f275";
        }

        .fa-map-pin:before {
            content: "\f276";
        }

        .fa-map-signs:before {
            content: "\f277";
        }

        .fa-map-o:before {
            content: "\f278";
        }

        .fa-map:before {
            content: "\f279";
        }

        .fa-commenting:before {
            content: "\f27a";
        }

        .fa-commenting-o:before {
            content: "\f27b";
        }

        .fa-houzz:before {
            content: "\f27c";
        }

        .fa-vimeo:before {
            content: "\f27d";
        }

        .fa-black-tie:before {
            content: "\f27e";
        }

        .fa-fonticons:before {
            content: "\f280";
        }

        .fa-reddit-alien:before {
            content: "\f281";
        }

        .fa-edge:before {
            content: "\f282";
        }

        .fa-credit-card-alt:before {
            content: "\f283";
        }

        .fa-codiepie:before {
            content: "\f284";
        }

        .fa-modx:before {
            content: "\f285";
        }

        .fa-fort-awesome:before {
            content: "\f286";
        }

        .fa-usb:before {
            content: "\f287";
        }

        .fa-product-hunt:before {
            content: "\f288";
        }

        .fa-mixcloud:before {
            content: "\f289";
        }

        .fa-scribd:before {
            content: "\f28a";
        }

        .fa-pause-circle:before {
            content: "\f28b";
        }

        .fa-pause-circle-o:before {
            content: "\f28c";
        }

        .fa-stop-circle:before {
            content: "\f28d";
        }

        .fa-stop-circle-o:before {
            content: "\f28e";
        }

        .fa-shopping-bag:before {
            content: "\f290";
        }

        .fa-shopping-basket:before {
            content: "\f291";
        }

        .fa-hashtag:before {
            content: "\f292";
        }

        .fa-bluetooth:before {
            content: "\f293";
        }

        .fa-bluetooth-b:before {
            content: "\f294";
        }

        .fa-percent:before {
            content: "\f295";
        }

        .fa-gitlab:before {
            content: "\f296";
        }

        .fa-wpbeginner:before {
            content: "\f297";
        }

        .fa-wpforms:before {
            content: "\f298";
        }

        .fa-envira:before {
            content: "\f299";
        }

        .fa-universal-access:before {
            content: "\f29a";
        }

        .fa-wheelchair-alt:before {
            content: "\f29b";
        }

        .fa-question-circle-o:before {
            content: "\f29c";
        }

        .fa-blind:before {
            content: "\f29d";
        }

        .fa-audio-description:before {
            content: "\f29e";
        }

        .fa-volume-control-phone:before {
            content: "\f2a0";
        }

        .fa-braille:before {
            content: "\f2a1";
        }

        .fa-assistive-listening-systems:before {
            content: "\f2a2";
        }

        .fa-asl-interpreting:before,
        .fa-american-sign-language-interpreting:before {
            content: "\f2a3";
        }

        .fa-deafness:before,
        .fa-hard-of-hearing:before,
        .fa-deaf:before {
            content: "\f2a4";
        }

        .fa-glide:before {
            content: "\f2a5";
        }

        .fa-glide-g:before {
            content: "\f2a6";
        }

        .fa-signing:before,
        .fa-sign-language:before {
            content: "\f2a7";
        }

        .fa-low-vision:before {
            content: "\f2a8";
        }

        .fa-viadeo:before {
            content: "\f2a9";
        }

        .fa-viadeo-square:before {
            content: "\f2aa";
        }

        .fa-snapchat:before {
            content: "\f2ab";
        }

        .fa-snapchat-ghost:before {
            content: "\f2ac";
        }

        .fa-snapchat-square:before {
            content: "\f2ad";
        }

        .fa-pied-piper:before {
            content: "\f2ae";
        }

        .fa-first-order:before {
            content: "\f2b0";
        }

        .fa-yoast:before {
            content: "\f2b1";
        }

        .fa-themeisle:before {
            content: "\f2b2";
        }

        .fa-google-plus-circle:before,
        .fa-google-plus-official:before {
            content: "\f2b3";
        }

        .fa-fa:before,
        .fa-font-awesome:before {
            content: "\f2b4";
        }

        .fa-handshake-o:before {
            content: "\f2b5";
        }

        .fa-envelope-open:before {
            content: "\f2b6";
        }

        .fa-envelope-open-o:before {
            content: "\f2b7";
        }

        .fa-linode:before {
            content: "\f2b8";
        }

        .fa-address-book:before {
            content: "\f2b9";
        }

        .fa-address-book-o:before {
            content: "\f2ba";
        }

        .fa-vcard:before,
        .fa-address-card:before {
            content: "\f2bb";
        }

        .fa-vcard-o:before,
        .fa-address-card-o:before {
            content: "\f2bc";
        }

        .fa-user-circle:before {
            content: "\f2bd";
        }

        .fa-user-circle-o:before {
            content: "\f2be";
        }

        .fa-user-o:before {
            content: "\f2c0";
        }

        .fa-id-badge:before {
            content: "\f2c1";
        }

        .fa-drivers-license:before,
        .fa-id-card:before {
            content: "\f2c2";
        }

        .fa-drivers-license-o:before,
        .fa-id-card-o:before {
            content: "\f2c3";
        }

        .fa-quora:before {
            content: "\f2c4";
        }

        .fa-free-code-camp:before {
            content: "\f2c5";
        }

        .fa-telegram:before {
            content: "\f2c6";
        }

        .fa-thermometer-4:before,
        .fa-thermometer:before,
        .fa-thermometer-full:before {
            content: "\f2c7";
        }

        .fa-thermometer-3:before,
        .fa-thermometer-three-quarters:before {
            content: "\f2c8";
        }

        .fa-thermometer-2:before,
        .fa-thermometer-half:before {
            content: "\f2c9";
        }

        .fa-thermometer-1:before,
        .fa-thermometer-quarter:before {
            content: "\f2ca";
        }

        .fa-thermometer-0:before,
        .fa-thermometer-empty:before {
            content: "\f2cb";
        }

        .fa-shower:before {
            content: "\f2cc";
        }

        .fa-bathtub:before,
        .fa-s15:before,
        .fa-bath:before {
            content: "\f2cd";
        }

        .fa-podcast:before {
            content: "\f2ce";
        }

        .fa-window-maximize:before {
            content: "\f2d0";
        }

        .fa-window-minimize:before {
            content: "\f2d1";
        }

        .fa-window-restore:before {
            content: "\f2d2";
        }

        .fa-times-rectangle:before,
        .fa-window-close:before {
            content: "\f2d3";
        }

        .fa-times-rectangle-o:before,
        .fa-window-close-o:before {
            content: "\f2d4";
        }

        .fa-bandcamp:before {
            content: "\f2d5";
        }

        .fa-grav:before {
            content: "\f2d6";
        }

        .fa-etsy:before {
            content: "\f2d7";
        }

        .fa-imdb:before {
            content: "\f2d8";
        }

        .fa-ravelry:before {
            content: "\f2d9";
        }

        .fa-eercast:before {
            content: "\f2da";
        }

        .fa-microchip:before {
            content: "\f2db";
        }

        .fa-snowflake-o:before {
            content: "\f2dc";
        }

        .fa-superpowers:before {
            content: "\f2dd";
        }

        .fa-wpexplorer:before {
            content: "\f2de";
        }

        .fa-meetup:before {
            content: "\f2e0";
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .sr-only-focusable:active,
        .sr-only-focusable:focus {
            position: static;
            width: auto;
            height: auto;
            margin: 0;
            overflow: visible;
            clip: auto;
        }

        :root,
        [data-bs-theme="light"] {
            --bs-blue: #0d6efd;
            --bs-indigo: #6610f2;
            --bs-purple: #6f42c1;
            --bs-pink: #d63384;
            --bs-red: #dc3545;
            --bs-orange: #fd7e14;
            --bs-yellow: #ffc107;
            --bs-green: #198754;
            --bs-teal: #20c997;
            --bs-cyan: #0dcaf0;
            --bs-black: #000;
            --bs-white: #fff;
            --bs-gray: #6c757d;
            --bs-gray-dark: #343a40;
            --bs-gray-100: #f8f9fa;
            --bs-gray-200: #e9ecef;
            --bs-gray-300: #dee2e6;
            --bs-gray-400: #ced4da;
            --bs-gray-500: #adb5bd;
            --bs-gray-600: #6c757d;
            --bs-gray-700: #495057;
            --bs-gray-800: #343a40;
            --bs-gray-900: #212529;
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
            --bs-primary-rgb: 13, 110, 253;
            --bs-secondary-rgb: 108, 117, 125;
            --bs-success-rgb: 25, 135, 84;
            --bs-info-rgb: 13, 202, 240;
            --bs-warning-rgb: 255, 193, 7;
            --bs-danger-rgb: 220, 53, 69;
            --bs-light-rgb: 248, 249, 250;
            --bs-dark-rgb: 33, 37, 41;
            --bs-primary-text-emphasis: #052c65;
            --bs-secondary-text-emphasis: #2b2f32;
            --bs-success-text-emphasis: #0a3622;
            --bs-info-text-emphasis: #055160;
            --bs-warning-text-emphasis: #664d03;
            --bs-danger-text-emphasis: #58151c;
            --bs-light-text-emphasis: #495057;
            --bs-dark-text-emphasis: #495057;
            --bs-primary-bg-subtle: #cfe2ff;
            --bs-secondary-bg-subtle: #e2e3e5;
            --bs-success-bg-subtle: #d1e7dd;
            --bs-info-bg-subtle: #cff4fc;
            --bs-warning-bg-subtle: #fff3cd;
            --bs-danger-bg-subtle: #f8d7da;
            --bs-light-bg-subtle: #fcfcfd;
            --bs-dark-bg-subtle: #ced4da;
            --bs-primary-border-subtle: #9ec5fe;
            --bs-secondary-border-subtle: #c4c8cb;
            --bs-success-border-subtle: #a3cfbb;
            --bs-info-border-subtle: #9eeaf9;
            --bs-warning-border-subtle: #ffe69c;
            --bs-danger-border-subtle: #f1aeb5;
            --bs-light-border-subtle: #e9ecef;
            --bs-dark-border-subtle: #adb5bd;
            --bs-white-rgb: 255, 255, 255;
            --bs-black-rgb: 0, 0, 0;
            --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto,
                "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif,
                "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol",
                "Noto Color Emoji";
            --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas,
                "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg,
                    rgba(255, 255, 255, 0.15),
                    rgba(255, 255, 255, 0));
            --bs-body-font-family: var(--bs-font-sans-serif);
            --bs-body-font-size: 1rem;
            --bs-body-font-weight: 400;
            --bs-body-line-height: 1.5;
            --bs-body-color: #212529;
            --bs-body-color-rgb: 33, 37, 41;
            --bs-body-bg: #fff;
            --bs-body-bg-rgb: 255, 255, 255;
            --bs-emphasis-color: #000;
            --bs-emphasis-color-rgb: 0, 0, 0;
            --bs-secondary-color: rgba(33, 37, 41, 0.75);
            --bs-secondary-color-rgb: 33, 37, 41;
            --bs-secondary-bg: #e9ecef;
            --bs-secondary-bg-rgb: 233, 236, 239;
            --bs-tertiary-color: rgba(33, 37, 41, 0.5);
            --bs-tertiary-color-rgb: 33, 37, 41;
            --bs-tertiary-bg: #f8f9fa;
            --bs-tertiary-bg-rgb: 248, 249, 250;
            --bs-heading-color: inherit;
            --bs-link-color: #0d6efd;
            --bs-link-color-rgb: 13, 110, 253;
            --bs-link-decoration: underline;
            --bs-link-hover-color: #0a58ca;
            --bs-link-hover-color-rgb: 10, 88, 202;
            --bs-code-color: #d63384;
            --bs-highlight-bg: #fff3cd;
            --bs-border-width: 1px;
            --bs-border-style: solid;
            --bs-border-color: #dee2e6;
            --bs-border-color-translucent: rgba(0, 0, 0, 0.175);
            --bs-border-radius: 0.375rem;
            --bs-border-radius-sm: 0.25rem;
            --bs-border-radius-lg: 0.5rem;
            --bs-border-radius-xl: 1rem;
            --bs-border-radius-xxl: 2rem;
            --bs-border-radius-2xl: var(--bs-border-radius-xxl);
            --bs-border-radius-pill: 50rem;
            --bs-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --bs-box-shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --bs-box-shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --bs-box-shadow-inset: inset 0 1px 2px rgba(0, 0, 0, 0.075);
            --bs-focus-ring-width: 0.25rem;
            --bs-focus-ring-opacity: 0.25;
            --bs-focus-ring-color: rgba(13, 110, 253, 0.25);
            --bs-form-valid-color: #198754;
            --bs-form-valid-border-color: #198754;
            --bs-form-invalid-color: #dc3545;
            --bs-form-invalid-border-color: #dc3545;
        }

        [data-bs-theme="dark"] {
            color-scheme: dark;
            --bs-body-color: #dee2e6;
            --bs-body-color-rgb: 222, 226, 230;
            --bs-body-bg: #212529;
            --bs-body-bg-rgb: 33, 37, 41;
            --bs-emphasis-color: #fff;
            --bs-emphasis-color-rgb: 255, 255, 255;
            --bs-secondary-color: rgba(222, 226, 230, 0.75);
            --bs-secondary-color-rgb: 222, 226, 230;
            --bs-secondary-bg: #343a40;
            --bs-secondary-bg-rgb: 52, 58, 64;
            --bs-tertiary-color: rgba(222, 226, 230, 0.5);
            --bs-tertiary-color-rgb: 222, 226, 230;
            --bs-tertiary-bg: #2b3035;
            --bs-tertiary-bg-rgb: 43, 48, 53;
            --bs-primary-text-emphasis: #6ea8fe;
            --bs-secondary-text-emphasis: #a7acb1;
            --bs-success-text-emphasis: #75b798;
            --bs-info-text-emphasis: #6edff6;
            --bs-warning-text-emphasis: #ffda6a;
            --bs-danger-text-emphasis: #ea868f;
            --bs-light-text-emphasis: #f8f9fa;
            --bs-dark-text-emphasis: #dee2e6;
            --bs-primary-bg-subtle: #031633;
            --bs-secondary-bg-subtle: #161719;
            --bs-success-bg-subtle: #051b11;
            --bs-info-bg-subtle: #032830;
            --bs-warning-bg-subtle: #332701;
            --bs-danger-bg-subtle: #2c0b0e;
            --bs-light-bg-subtle: #343a40;
            --bs-dark-bg-subtle: #1a1d20;
            --bs-primary-border-subtle: #084298;
            --bs-secondary-border-subtle: #41464b;
            --bs-success-border-subtle: #0f5132;
            --bs-info-border-subtle: #087990;
            --bs-warning-border-subtle: #997404;
            --bs-danger-border-subtle: #842029;
            --bs-light-border-subtle: #495057;
            --bs-dark-border-subtle: #343a40;
            --bs-heading-color: inherit;
            --bs-link-color: #6ea8fe;
            --bs-link-hover-color: #8bb9fe;
            --bs-link-color-rgb: 110, 168, 254;
            --bs-link-hover-color-rgb: 139, 185, 254;
            --bs-code-color: #e685b5;
            --bs-border-color: #495057;
            --bs-border-color-translucent: rgba(255, 255, 255, 0.15);
            --bs-form-valid-color: #75b798;
            --bs-form-valid-border-color: #75b798;
            --bs-form-invalid-color: #ea868f;
            --bs-form-invalid-border-color: #ea868f;
        }

        .alert {
            --bs-alert-bg: transparent;
            --bs-alert-padding-x: 1rem;
            --bs-alert-padding-y: 1rem;
            --bs-alert-margin-bottom: 1rem;
            --bs-alert-color: inherit;
            --bs-alert-border-color: transparent;
            --bs-alert-border: var(--bs-border-width) solid var(--bs-alert-border-color);
            --bs-alert-border-radius: var(--bs-border-radius);
            --bs-alert-link-color: inherit;
            position: relative;
            padding: var(--bs-alert-padding-y) var(--bs-alert-padding-x);
            margin-bottom: var(--bs-alert-margin-bottom);
            color: var(--bs-alert-color);
            background-color: var(--bs-alert-bg);
            border: var(--bs-alert-border);
            border-radius: var(--bs-alert-border-radius);
        }

        .alert-heading {
            color: inherit;
        }

        .alert-link {
            font-weight: 700;
            color: var(--bs-alert-link-color);
        }

        .alert-dismissible {
            padding-right: 3rem;
        }

        .alert-dismissible .btn-close {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 2;
            padding: 1.25rem 1rem;
        }

        .alert-primary {
            --bs-alert-color: var(--bs-primary-text-emphasis);
            --bs-alert-bg: var(--bs-primary-bg-subtle);
            --bs-alert-border-color: var(--bs-primary-border-subtle);
            --bs-alert-link-color: var(--bs-primary-text-emphasis);
        }

        .alert-secondary {
            --bs-alert-color: var(--bs-secondary-text-emphasis);
            --bs-alert-bg: var(--bs-secondary-bg-subtle);
            --bs-alert-border-color: var(--bs-secondary-border-subtle);
            --bs-alert-link-color: var(--bs-secondary-text-emphasis);
        }

        .alert-success {
            --bs-alert-color: var(--bs-success-text-emphasis);
            --bs-alert-bg: var(--bs-success-bg-subtle);
            --bs-alert-border-color: var(--bs-success-border-subtle);
            --bs-alert-link-color: var(--bs-success-text-emphasis);
        }

        .alert-info {
            --bs-alert-color: var(--bs-info-text-emphasis);
            --bs-alert-bg: var(--bs-info-bg-subtle);
            --bs-alert-border-color: var(--bs-info-border-subtle);
            --bs-alert-link-color: var(--bs-info-text-emphasis);
        }

        .alert-warning {
            --bs-alert-color: var(--bs-warning-text-emphasis);
            --bs-alert-bg: var(--bs-warning-bg-subtle);
            --bs-alert-border-color: var(--bs-warning-border-subtle);
            --bs-alert-link-color: var(--bs-warning-text-emphasis);
        }

        .alert-danger {
            --bs-alert-color: var(--bs-danger-text-emphasis);
            --bs-alert-bg: var(--bs-danger-bg-subtle);
            --bs-alert-border-color: var(--bs-danger-border-subtle);
            --bs-alert-link-color: var(--bs-danger-text-emphasis);
        }

        .alert-light {
            --bs-alert-color: var(--bs-light-text-emphasis);
            --bs-alert-bg: var(--bs-light-bg-subtle);
            --bs-alert-border-color: var(--bs-light-border-subtle);
            --bs-alert-link-color: var(--bs-light-text-emphasis);
        }

        .alert-dark {
            --bs-alert-color: var(--bs-dark-text-emphasis);
            --bs-alert-bg: var(--bs-dark-bg-subtle);
            --bs-alert-border-color: var(--bs-dark-border-subtle);
            --bs-alert-link-color: var(--bs-dark-text-emphasis);
        }

        .alert-primary-dark {
            --bs-alert-color: #6ea8fe;
            --bs-alert-bg: #031633;
            --bs-alert-border-color: #084298;
            --bs-alert-link-color: #6ea8fe;
        }

        .alert-secondary-dark {
            --bs-alert-color: #a7acb1;
            --bs-alert-bg: #161719;
            --bs-alert-border-color: #41464b;
            --bs-alert-link-color: #a7acb1;
        }

        .alert-success-dark {
            --bs-alert-color: #75b798;
            --bs-alert-bg: #051b11;
            --bs-alert-border-color: #0f5132;
            --bs-alert-link-color: #75b798;
        }

        .alert-info-dark {
            --bs-alert-color: #6edff6;
            --bs-alert-bg: #032830;
            --bs-alert-border-color: #087990;
            --bs-alert-link-color: #6edff6;
        }

        .alert-warning-dark {
            --bs-alert-color: #ffda6a;
            --bs-alert-bg: #332701;
            --bs-alert-border-color: #997404;
            --bs-alert-link-color: #ffda6a;
        }

        .alert-danger-dark {
            --bs-alert-color: #ea868f;
            --bs-alert-bg: #2c0b0e;
            --bs-alert-border-color: #842029;
            --bs-alert-link-color: #ea868f;
        }

        .alert-light-dark {
            --bs-alert-color: #f8f9fa;
            --bs-alert-bg: #343a40;
            --bs-alert-border-color: #495057;
            --bs-alert-link-color: #f8f9fa;
        }

        .alert-dark-dark {
            --bs-alert-color: #dee2e6;
            --bs-alert-bg: #1a1d20;
            --bs-alert-border-color: #343a40;
            --bs-alert-link-color: #dee2e6;
        }

        /*! tailwindcss v3.3.5 | MIT License | https://tailwindcss.com
        */

        /*
        1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
        2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
        */

        *,
        ::before,
        ::after {
            box-sizing: border-box;
            /* 1 */
            border-width: 0;
            /* 2 */
            border-style: solid;
            /* 2 */
            border-color: #e5e7eb;
            /* 2 */
        }

        ::before,
        ::after {
            --tw-content: '';
        }

        /*
        1. Use a consistent sensible line-height in all browsers.
        2. Prevent adjustments of font size after orientation changes in iOS.
        3. Use a more readable tab size.
        4. Use the user's configured `sans` font-family by default.
        5. Use the user's configured `sans` font-feature-settings by default.
        6. Use the user's configured `sans` font-variation-settings by default.
        */

        html {
            line-height: 1.5;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
            -moz-tab-size: 4;
            /* 3 */
            -o-tab-size: 4;
            tab-size: 4;
            /* 3 */
            /* font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"; */
            /* 4 */
            font-feature-settings: normal;
            /* 5 */
            font-variation-settings: normal;
            /* 6 */
        }

        /*
        1. Remove the margin in all browsers.
        2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
        */

        body {
            margin: 0;
            /* 1 */
            line-height: inherit;
            /* 2 */
        }

        /*
        1. Add the correct height in Firefox.
        2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
        3. Ensure horizontal rules are visible by default.
        */

        hr {
            height: 0;
            /* 1 */
            color: inherit;
            /* 2 */
            border-top-width: 1px;
            /* 3 */
        }

        /*
        Add the correct text decoration in Chrome, Edge, and Safari.
        */

        abbr:where([title]) {
            -webkit-text-decoration: underline dotted;
            text-decoration: underline dotted;
        }

        /*
        Remove the default font size and weight for headings.
        */

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit;
        }

        /*
        Reset links to optimize for opt-in styling instead of opt-out.
        */

        a {
            color: inherit;
            text-decoration: inherit;
        }

        /*
        Add the correct font weight in Edge and Safari.
        */

        b,
        strong {
            font-weight: bolder;
        }

        /*
        1. Use the user's configured `mono` font family by default.
        2. Correct the odd `em` font sizing in all browsers.
        */

        code,
        kbd,
        samp,
        pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            /* 1 */
            font-size: 1em;
            /* 2 */
        }

        /*
        Add the correct font size in all browsers.
        */

        small {
            font-size: 80%;
        }

        /*
        Prevent `sub` and `sup` elements from affecting the line height in all browsers.
        */

        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        /*
        1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
        2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
        3. Remove gaps between table borders by default.
        */

        table {
            text-indent: 0;
            /* 1 */
            border-color: inherit;
            /* 2 */
            border-collapse: collapse;
            /* 3 */
        }

        /*
        1. Change the font styles in all browsers.
        2. Remove the margin in Firefox and Safari.
        3. Remove default padding in all browsers.
        */

        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            /* 1 */
            font-feature-settings: inherit;
            /* 1 */
            font-variation-settings: inherit;
            /* 1 */
            font-size: 100%;
            /* 1 */
            font-weight: inherit;
            /* 1 */
            line-height: inherit;
            /* 1 */
            color: inherit;
            /* 1 */
            margin: 0;
            /* 2 */
            padding: 0;
            /* 3 */
        }

        /*
        Remove the inheritance of text transform in Edge and Firefox.
        */

        button,
        select {
            text-transform: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Remove default button styles.
        */

        button,
        [type='button'],
        [type='reset'],
        [type='submit'] {
            -webkit-appearance: button;
            /* 1 */
            background-color: transparent;
            /* 2 */
            background-image: none;
            /* 2 */
        }

        /*
        Use the modern Firefox focus style for all focusable elements.
        */

        :-moz-focusring {
            outline: auto;
        }

        /*
        Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
        */

        :-moz-ui-invalid {
            box-shadow: none;
        }

        /*
        Add the correct vertical alignment in Chrome and Firefox.
        */

        progress {
            vertical-align: baseline;
        }

        /*
        Correct the cursor style of increment and decrement buttons in Safari.
        */

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto;
        }

        /*
        1. Correct the odd appearance in Chrome and Safari.
        2. Correct the outline style in Safari.
        */

        [type='search'] {
            -webkit-appearance: textfield;
            /* 1 */
            outline-offset: -2px;
            /* 2 */
        }

        /*
        Remove the inner padding in Chrome and Safari on macOS.
        */

        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Change font properties to `inherit` in Safari.
        */

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            /* 1 */
            font: inherit;
            /* 2 */
        }

        /*
        Add the correct display in Chrome and Safari.
        */

        summary {
            display: list-item;
        }

        /*
        Removes the default spacing and border for appropriate elements.
        */

        blockquote,
        dl,
        dd,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        figure,
        p,
        pre {
            margin: 0;
        }

        fieldset {
            margin: 0;
            padding: 0;
        }

        legend {
            padding: 0;
        }

        ol,
        ul,
        menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        /*
        Reset default styling for dialogs.
        */

        dialog {
            padding: 0;
        }

        /*
        Prevent resizing textareas horizontally by default.
        */

        textarea {
            resize: vertical;
        }

        /*
        1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
        2. Set the default placeholder color to the user's configured gray 400 color.
        */

        input::-moz-placeholder,
        textarea::-moz-placeholder {
            opacity: 1;
            /* 1 */
            color: #9ca3af;
            /* 2 */
        }

        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            /* 1 */
            color: #9ca3af;
            /* 2 */
        }

        /*
        Set the default cursor for buttons.
        */

        button,
        [role="button"] {
            cursor: pointer;
        }

        /*
        Make sure disabled buttons don't get the pointer cursor.
        */

        :disabled {
            cursor: default;
        }

        /*
        1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
        2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
        This can trigger a poorly considered lint error in some tools but is included by design.
        */

        img,
        svg,
        video,
        canvas,
        audio,
        iframe,
        embed,
        object {
            display: block;
            /* 1 */
            vertical-align: middle;
            /* 2 */
        }

        /*
        Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
        */

        img,
        video {
            max-width: 100%;
            height: auto;
        }

        /* Make elements with the HTML hidden attribute stay hidden by default */

        [hidden] {
            display: none;
        }

        [type='text'],
        input:where(:not([type])),
        [type='email'],
        [type='url'],
        [type='password'],
        [type='number'],
        [type='date'],
        [type='datetime-local'],
        [type='month'],
        [type='search'],
        [type='tel'],
        [type='time'],
        [type='week'],
        [multiple],
        textarea,
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #fff;
            border-color: #6b7280;
            border-width: 1px;
            border-radius: 0px;
            padding-top: 0.5rem;
            padding-right: 0.75rem;
            padding-bottom: 0.5rem;
            padding-left: 0.75rem;
            font-size: 1rem;
            line-height: 1.5rem;
            --tw-shadow: 0 0 #0000;
        }

        [type='text']:focus,
        input:where(:not([type])):focus,
        [type='email']:focus,
        [type='url']:focus,
        [type='password']:focus,
        [type='number']:focus,
        [type='date']:focus,
        [type='datetime-local']:focus,
        [type='month']:focus,
        [type='search']:focus,
        [type='tel']:focus,
        [type='time']:focus,
        [type='week']:focus,
        [multiple]:focus,
        textarea:focus,
        select:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-inset: var(--tw-empty,
                    /*!*/
                    /*!*/
                );
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: #2563eb;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
            border-color: #2563eb;
        }

        input::-moz-placeholder,
        textarea::-moz-placeholder {
            color: #6b7280;
            opacity: 1;
        }

        input::placeholder,
        textarea::placeholder {
            color: #6b7280;
            opacity: 1;
        }

        ::-webkit-datetime-edit-fields-wrapper {
            padding: 0;
        }

        ::-webkit-date-and-time-value {
            min-height: 1.5em;
            text-align: inherit;
        }

        ::-webkit-datetime-edit {
            display: inline-flex;
        }

        ::-webkit-datetime-edit,
        ::-webkit-datetime-edit-year-field,
        ::-webkit-datetime-edit-month-field,
        ::-webkit-datetime-edit-day-field,
        ::-webkit-datetime-edit-hour-field,
        ::-webkit-datetime-edit-minute-field,
        ::-webkit-datetime-edit-second-field,
        ::-webkit-datetime-edit-millisecond-field,
        ::-webkit-datetime-edit-meridiem-field {
            padding-top: 0;
            padding-bottom: 0;
        }

        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        [multiple],[size]:where(select:not([size="1"])) {
            background-image: initial;
            background-position: initial;
            background-repeat: unset;
            background-size: initial;
            padding-right: 0.75rem;
            -webkit-print-color-adjust: unset;
            print-color-adjust: unset;
        }

        [type='checkbox'],
        [type='radio'] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            display: inline-block;
            vertical-align: middle;
            background-origin: border-box;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            flex-shrink: 0;
            height: 1rem;
            width: 1rem;
            color: #2563eb;
            background-color: #fff;
            border-color: #6b7280;
            border-width: 1px;
            --tw-shadow: 0 0 #0000;
        }

        [type='checkbox'] {
            border-radius: 0px;
        }

        [type='radio'] {
            border-radius: 100%;
        }

        [type='checkbox']:focus,
        [type='radio']:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-inset: var(--tw-empty,
                    /*!*/
                    /*!*/
                );
            --tw-ring-offset-width: 2px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: #2563eb;
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
        }

        [type='checkbox']:checked,
        [type='radio']:checked {
            border-color: transparent;
            background-color: currentColor;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        [type='checkbox']:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }

        [type='radio']:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
        }

        [type='checkbox']:checked:hover,
        [type='checkbox']:checked:focus,
        [type='radio']:checked:hover,
        [type='radio']:checked:focus {
            border-color: transparent;
            background-color: currentColor;
        }

        [type='checkbox']:indeterminate {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3e%3cpath stroke='white' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3e%3c/svg%3e");
            border-color: transparent;
            background-color: currentColor;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        [type='checkbox']:indeterminate:hover,
        [type='checkbox']:indeterminate:focus {
            border-color: transparent;
            background-color: currentColor;
        }

        [type='file'] {
            background: unset;
            border-color: inherit;
            border-width: 0;
            border-radius: 0;
            padding: 0;
            font-size: unset;
            line-height: inherit;
        }

        [type='file']:focus {
            outline: 1px solid ButtonText;
            outline: 1px auto -webkit-focus-ring-color;
        }

        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
        }

        .prose {
            color: var(--tw-prose-body);
            max-width: 65ch;
        }

        .prose :where(p):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 1.25em;
            margin-bottom: 1.25em;
        }

        .prose :where([class~="lead"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-lead);
            font-size: 1.25em;
            line-height: 1.6;
            margin-top: 1.2em;
            margin-bottom: 1.2em;
        }

        .prose :where(a):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-links);
            text-decoration: underline;
            font-weight: 500;
        }

        .prose :where(strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-bold);
            font-weight: 600;
        }

        .prose :where(a strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(blockquote strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(thead th strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(ol):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: decimal;
            margin-top: 1.25em;
            margin-bottom: 1.25em;
            padding-left: 1.625em;
        }

        .prose :where(ol[type="A"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: upper-alpha;
        }

        .prose :where(ol[type="a"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: lower-alpha;
        }

        .prose :where(ol[type="A" s]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: upper-alpha;
        }

        .prose :where(ol[type="a" s]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: lower-alpha;
        }

        .prose :where(ol[type="I"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: upper-roman;
        }

        .prose :where(ol[type="i"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: lower-roman;
        }

        .prose :where(ol[type="I" s]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: upper-roman;
        }

        .prose :where(ol[type="i" s]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: lower-roman;
        }

        .prose :where(ol[type="1"]):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: decimal;
        }

        .prose :where(ul):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            list-style-type: disc;
            margin-top: 1.25em;
            margin-bottom: 1.25em;
            padding-left: 1.625em;
        }

        .prose :where(ol > li):not(:where([class~="not-prose"], [class~="not-prose"] *))::marker {
            font-weight: 400;
            color: var(--tw-prose-counters);
        }

        .prose :where(ul > li):not(:where([class~="not-prose"], [class~="not-prose"] *))::marker {
            color: var(--tw-prose-bullets);
        }

        .prose :where(dt):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 600;
            margin-top: 1.25em;
        }

        .prose :where(hr):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            border-color: var(--tw-prose-hr);
            border-top-width: 1px;
            margin-top: 3em;
            margin-bottom: 3em;
        }

        .prose :where(blockquote):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 500;
            font-style: italic;
            color: var(--tw-prose-quotes);
            border-left-width: 0.25rem;
            border-left-color: var(--tw-prose-quote-borders);
            quotes: "\201C" "\201D" "\2018" "\2019";
            margin-top: 1.6em;
            margin-bottom: 1.6em;
            padding-left: 1em;
        }

        .prose :where(blockquote p:first-of-type):not(:where([class~="not-prose"], [class~="not-prose"] *))::before {
            content: open-quote;
        }

        .prose :where(blockquote p:last-of-type):not(:where([class~="not-prose"], [class~="not-prose"] *))::after {
            content: close-quote;
        }

        .prose :where(h1):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 800;
            font-size: 2.25em;
            margin-top: 0;
            margin-bottom: 0.8888889em;
            line-height: 1.1111111;
        }

        .prose :where(h1 strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 900;
            color: inherit;
        }

        .prose :where(h2):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 700;
            font-size: 1.5em;
            margin-top: 2em;
            margin-bottom: 1em;
            line-height: 1.3333333;
        }

        .prose :where(h2 strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 800;
            color: inherit;
        }

        .prose :where(h3):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 600;
            font-size: 1.25em;
            margin-top: 1.6em;
            margin-bottom: 0.6em;
            line-height: 1.6;
        }

        .prose :where(h3 strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 700;
            color: inherit;
        }

        .prose :where(h4):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 600;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            line-height: 1.5;
        }

        .prose :where(h4 strong):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 700;
            color: inherit;
        }

        .prose :where(img):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 2em;
            margin-bottom: 2em;
        }

        .prose :where(picture):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            display: block;
            margin-top: 2em;
            margin-bottom: 2em;
        }

        .prose :where(kbd):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            font-weight: 500;
            font-family: inherit;
            color: var(--tw-prose-kbd);
            box-shadow: 0 0 0 1px rgb(var(--tw-prose-kbd-shadows) / 10%), 0 3px 0 rgb(var(--tw-prose-kbd-shadows) / 10%);
            font-size: 0.875em;
            border-radius: 0.3125rem;
            padding-top: 0.1875em;
            padding-right: 0.375em;
            padding-bottom: 0.1875em;
            padding-left: 0.375em;
        }

        .prose :where(code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-code);
            font-weight: 600;
            font-size: 0.875em;
        }

        .prose :where(code):not(:where([class~="not-prose"], [class~="not-prose"] *))::before {
            content: "`";
        }

        .prose :where(code):not(:where([class~="not-prose"], [class~="not-prose"] *))::after {
            content: "`";
        }

        .prose :where(a code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(h1 code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(h2 code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
            font-size: 0.875em;
        }

        .prose :where(h3 code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
            font-size: 0.9em;
        }

        .prose :where(h4 code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(blockquote code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(thead th code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: inherit;
        }

        .prose :where(pre):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-pre-code);
            background-color: var(--tw-prose-pre-bg);
            overflow-x: auto;
            font-weight: 400;
            font-size: 0.875em;
            line-height: 1.7142857;
            margin-top: 1.7142857em;
            margin-bottom: 1.7142857em;
            border-radius: 0.375rem;
            padding-top: 0.8571429em;
            padding-right: 1.1428571em;
            padding-bottom: 0.8571429em;
            padding-left: 1.1428571em;
        }

        .prose :where(pre code):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            background-color: transparent;
            border-width: 0;
            border-radius: 0;
            padding: 0;
            font-weight: inherit;
            color: inherit;
            font-size: inherit;
            font-family: inherit;
            line-height: inherit;
        }

        .prose :where(pre code):not(:where([class~="not-prose"], [class~="not-prose"] *))::before {
            content: none;
        }

        .prose :where(pre code):not(:where([class~="not-prose"], [class~="not-prose"] *))::after {
            content: none;
        }

        .prose :where(table):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            width: 100%;
            table-layout: auto;
            text-align: left;
            margin-top: 2em;
            margin-bottom: 2em;
            font-size: 0.875em;
            line-height: 1.7142857;
        }

        .prose :where(thead):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            border-bottom-width: 1px;
            border-bottom-color: var(--tw-prose-th-borders);
        }

        .prose :where(thead th):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-headings);
            font-weight: 600;
            vertical-align: bottom;
            padding-right: 0.5714286em;
            padding-bottom: 0.5714286em;
            padding-left: 0.5714286em;
        }

        .prose :where(tbody tr):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            border-bottom-width: 1px;
            border-bottom-color: var(--tw-prose-td-borders);
        }

        .prose :where(tbody tr:last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            border-bottom-width: 0;
        }

        .prose :where(tbody td):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            vertical-align: baseline;
        }

        .prose :where(tfoot):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            border-top-width: 1px;
            border-top-color: var(--tw-prose-th-borders);
        }

        .prose :where(tfoot td):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            vertical-align: top;
        }

        .prose :where(figure > *):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
            margin-bottom: 0;
        }

        .prose :where(figcaption):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            color: var(--tw-prose-captions);
            font-size: 0.875em;
            line-height: 1.4285714;
            margin-top: 0.8571429em;
        }

        .prose {
            --tw-prose-body: #374151;
            --tw-prose-headings: #111827;
            --tw-prose-lead: #4b5563;
            --tw-prose-links: #111827;
            --tw-prose-bold: #111827;
            --tw-prose-counters: #6b7280;
            --tw-prose-bullets: #d1d5db;
            --tw-prose-hr: #e5e7eb;
            --tw-prose-quotes: #111827;
            --tw-prose-quote-borders: #e5e7eb;
            --tw-prose-captions: #6b7280;
            --tw-prose-kbd: #111827;
            --tw-prose-kbd-shadows: 17 24 39;
            --tw-prose-code: #111827;
            --tw-prose-pre-code: #e5e7eb;
            --tw-prose-pre-bg: #1f2937;
            --tw-prose-th-borders: #d1d5db;
            --tw-prose-td-borders: #e5e7eb;
            --tw-prose-invert-body: #d1d5db;
            --tw-prose-invert-headings: #fff;
            --tw-prose-invert-lead: #9ca3af;
            --tw-prose-invert-links: #fff;
            --tw-prose-invert-bold: #fff;
            --tw-prose-invert-counters: #9ca3af;
            --tw-prose-invert-bullets: #4b5563;
            --tw-prose-invert-hr: #374151;
            --tw-prose-invert-quotes: #f3f4f6;
            --tw-prose-invert-quote-borders: #374151;
            --tw-prose-invert-captions: #9ca3af;
            --tw-prose-invert-kbd: #fff;
            --tw-prose-invert-kbd-shadows: 255 255 255;
            --tw-prose-invert-code: #fff;
            --tw-prose-invert-pre-code: #d1d5db;
            --tw-prose-invert-pre-bg: rgb(0 0 0 / 50%);
            --tw-prose-invert-th-borders: #4b5563;
            --tw-prose-invert-td-borders: #374151;
            font-size: 1rem;
            line-height: 1.75;
        }

        .prose :where(picture > img):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
            margin-bottom: 0;
        }

        .prose :where(video):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 2em;
            margin-bottom: 2em;
        }

        .prose :where(li):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose :where(ol > li):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-left: 0.375em;
        }

        .prose :where(ul > li):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-left: 0.375em;
        }

        .prose :where(.prose > ul > li p):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0.75em;
            margin-bottom: 0.75em;
        }

        .prose :where(.prose > ul > li > *:first-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 1.25em;
        }

        .prose :where(.prose > ul > li > *:last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-bottom: 1.25em;
        }

        .prose :where(.prose > ol > li > *:first-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 1.25em;
        }

        .prose :where(.prose > ol > li > *:last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-bottom: 1.25em;
        }

        .prose :where(ul ul, ul ol, ol ul, ol ol):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0.75em;
            margin-bottom: 0.75em;
        }

        .prose :where(dl):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 1.25em;
            margin-bottom: 1.25em;
        }

        .prose :where(dd):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0.5em;
            padding-left: 1.625em;
        }

        .prose :where(hr + *):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
        }

        .prose :where(h2 + *):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
        }

        .prose :where(h3 + *):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
        }

        .prose :where(h4 + *):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
        }

        .prose :where(thead th:first-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-left: 0;
        }

        .prose :where(thead th:last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-right: 0;
        }

        .prose :where(tbody td, tfoot td):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-top: 0.5714286em;
            padding-right: 0.5714286em;
            padding-bottom: 0.5714286em;
            padding-left: 0.5714286em;
        }

        .prose :where(tbody td:first-child, tfoot td:first-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-left: 0;
        }

        .prose :where(tbody td:last-child, tfoot td:last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            padding-right: 0;
        }

        .prose :where(figure):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 2em;
            margin-bottom: 2em;
        }

        .prose :where(.prose > :first-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-top: 0;
        }

        .prose :where(.prose > :last-child):not(:where([class~="not-prose"], [class~="not-prose"] *)) {
            margin-bottom: 0;
        }

        .pointer-events-none {
            pointer-events: none;
        }

        .pointer-events-auto {
            pointer-events: auto;
        }

        .collapse {
            visibility: collapse;
        }

        .fixed {
            position: fixed;
        }

        .absolute {
            position: absolute;
        }

        .relative {
            position: relative;
        }

        .inset-0 {
            inset: 0px;
        }

        .bottom-4 {
            bottom: 1rem;
        }

        .end-0 {
            inset-inline-end: 0px;
        }

        .left-1\/2 {
            left: 50%;
        }

        .left-4 {
            left: 1rem;
        }

        .right-0 {
            right: 0px;
        }

        .right-4 {
            right: 1rem;
        }

        .start-0 {
            inset-inline-start: 0px;
        }

        .top-0 {
            top: 0px;
        }

        .top-4 {
            top: 1rem;
        }

        .z-0 {
            z-index: 0;
        }

        .z-10 {
            z-index: 10;
        }

        .z-50 {
            z-index: 50;
        }

        .col-span-1 {
            grid-column: span 1 / span 1;
        }

        .col-span-2 {
            grid-column: span 2 / span 2;
        }

        .col-span-3 {
            grid-column: span 3 / span 3;
        }

        .col-span-5 {
            grid-column: span 5 / span 5;
        }

        .col-span-6 {
            grid-column: span 6 / span 6;
        }

        .mx-1 {
            margin-left: 0.25rem;
            margin-right: 0.25rem;
        }

        .mx-1\.5 {
            margin-left: 0.375rem;
            margin-right: 0.375rem;
        }

        .mx-2 {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .my-auto {
            margin-top: auto;
            margin-bottom: auto;
        }

        .-me-0 {
            margin-inline-end: -0px;
        }

        .-me-0\.5 {
            margin-inline-end: -0.125rem;
        }

        .-me-1 {
            margin-inline-end: -0.25rem;
        }

        .-me-2 {
            margin-inline-end: -0.5rem;
        }

        .-ml-px {
            margin-left: -1px;
        }

        .-mt-px {
            margin-top: -1px;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .me-2 {
            margin-inline-end: 0.5rem;
        }

        .me-3 {
            margin-inline-end: 0.75rem;
        }

        .ml-1 {
            margin-left: 0.25rem;
        }

        .ml-12 {
            margin-left: 3rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .ml-3 {
            margin-left: 0.75rem;
        }

        .ml-4 {
            margin-left: 1rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .ms-1 {
            margin-inline-start: 0.25rem;
        }

        .ms-2 {
            margin-inline-start: 0.5rem;
        }

        .ms-3 {
            margin-inline-start: 0.75rem;
        }

        .ms-4 {
            margin-inline-start: 1rem;
        }

        .ms-6 {
            margin-inline-start: 1.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-10 {
            margin-top: 2.5rem;
        }

        .mt-12 {
            margin-top: 3rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-24 {
            margin-top: 6rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-5 {
            margin-top: 1.25rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .block {
            display: block;
        }

        .inline-block {
            display: inline-block;
        }

        .inline {
            display: inline;
        }

        .flex {
            display: flex;
        }

        .inline-flex {
            display: inline-flex;
        }

        .table {
            display: table;
        }

        .grid {
            display: grid;
        }

        .hidden {
            display: none;
        }

        .h-10 {
            height: 2.5rem;
        }

        .h-12 {
            height: 3rem;
        }

        .h-16 {
            height: 4rem;
        }

        .h-20 {
            height: 5rem;
        }

        .h-4 {
            height: 1rem;
        }

        .h-5 {
            height: 1.25rem;
        }

        .h-6 {
            height: 1.5rem;
        }

        .h-8 {
            height: 2rem;
        }

        .h-9 {
            height: 2.25rem;
        }

        .h-fit {
            height: -moz-fit-content;
            height: fit-content;
        }

        .min-h-screen {
            min-height: 100vh;
        }

        .w-0 {
            width: 0px;
        }

        .w-1\/2 {
            width: 50%;
        }

        .w-10 {
            width: 2.5rem;
        }

        .w-12 {
            width: 3rem;
        }

        .w-16 {
            width: 4rem;
        }

        .w-2\/3 {
            width: 66.666667%;
        }

        .w-20 {
            width: 5rem;
        }

        .w-3\/4 {
            width: 75%;
        }

        .w-4 {
            width: 1rem;
        }

        .w-4\/5 {
            width: 80%;
        }

        .w-48 {
            width: 12rem;
        }

        .w-5 {
            width: 1.25rem;
        }

        .w-6 {
            width: 1.5rem;
        }

        .w-60 {
            width: 15rem;
        }

        .w-8 {
            width: 2rem;
        }

        .w-96 {
            width: 24rem;
        }

        .w-auto {
            width: auto;
        }

        .w-full {
            width: 100%;
        }

        .min-w-0 {
            min-width: 0px;
        }

        .min-w-full {
            min-width: 100%;
        }

        .max-w-6xl {
            max-width: 72rem;
        }

        .max-w-7xl {
            max-width: 80rem;
        }

        .max-w-none {
            max-width: none;
        }

        .max-w-screen-xl {
            max-width: 1280px;
        }

        .max-w-sm {
            max-width: 24rem;
        }

        .max-w-xl {
            max-width: 36rem;
        }

        .flex-1 {
            flex: 1 1 0%;
        }

        .flex-shrink-0 {
            flex-shrink: 0;
        }

        .shrink-0 {
            flex-shrink: 0;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        .origin-top {
            transform-origin: top;
        }

        .-translate-x-1\/2 {
            --tw-translate-x: -50%;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .translate-y-0 {
            --tw-translate-y: 0px;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .translate-y-2 {
            --tw-translate-y: 0.5rem;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .translate-y-4 {
            --tw-translate-y: 1rem;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .rotate-0 {
            --tw-rotate: 0deg;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .rotate-90 {
            --tw-rotate: 90deg;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .scale-100 {
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .scale-95 {
            --tw-scale-x: .95;
            --tw-scale-y: .95;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .transform {
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
        }

        .cursor-default {
            cursor: default;
        }

        .cursor-not-allowed {
            cursor: not-allowed;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .select-none {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        .list-inside {
            list-style-position: inside;
        }

        .list-disc {
            list-style-type: disc;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .grid-cols-11 {
            grid-template-columns: repeat(11, minmax(0, 1fr));
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .grid-cols-6 {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .flex-row {
            flex-direction: row;
        }

        .flex-col {
            flex-direction: column;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .items-start {
            align-items: flex-start;
        }

        .items-end {
            align-items: flex-end;
        }

        .items-center {
            align-items: center;
        }

        .justify-end {
            justify-content: flex-end;
        }

        .justify-center {
            justify-content: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-items-center {
            justify-items: center;
        }

        .gap-1 {
            gap: 0.25rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .space-x-2> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 0;
            margin-right: calc(0.5rem * var(--tw-space-x-reverse));
            margin-left: calc(0.5rem * calc(1 - var(--tw-space-x-reverse)));
        }

        .space-x-8> :not([hidden])~ :not([hidden]) {
            --tw-space-x-reverse: 0;
            margin-right: calc(2rem * var(--tw-space-x-reverse));
            margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)));
        }

        .space-y-1> :not([hidden])~ :not([hidden]) {
            --tw-space-y-reverse: 0;
            margin-top: calc(0.25rem * calc(1 - var(--tw-space-y-reverse)));
            margin-bottom: calc(0.25rem * var(--tw-space-y-reverse));
        }

        .space-y-4> :not([hidden])~ :not([hidden]) {
            --tw-space-y-reverse: 0;
            margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse)));
            margin-bottom: calc(1rem * var(--tw-space-y-reverse));
        }

        .space-y-6> :not([hidden])~ :not([hidden]) {
            --tw-space-y-reverse: 0;
            margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
            margin-bottom: calc(1.5rem * var(--tw-space-y-reverse));
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .overflow-y-auto {
            overflow-y: auto;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .break-all {
            word-break: break-all;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        .rounded-b-none {
            border-bottom-right-radius: 0px;
            border-bottom-left-radius: 0px;
        }

        .rounded-l-md {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .rounded-r-md {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }

        .rounded-t-none {
            border-top-left-radius: 0px;
            border-top-right-radius: 0px;
        }

        .border {
            border-width: 1px;
        }

        .border-2 {
            border-width: 2px;
        }

        .border-b {
            border-bottom-width: 1px;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-b-4 {
            border-bottom-width: 4px;
        }

        .border-l-4 {
            border-left-width: 4px;
        }

        .border-r {
            border-right-width: 1px;
        }

        .border-t {
            border-top-width: 1px;
        }

        .border-blue-700 {
            --tw-border-opacity: 1;
            border-color: rgb(29 78 216 / var(--tw-border-opacity));
        }

        .border-gray-100 {
            --tw-border-opacity: 1;
            border-color: rgb(243 244 246 / var(--tw-border-opacity));
        }

        .border-gray-200 {
            --tw-border-opacity: 1;
            border-color: rgb(229 231 235 / var(--tw-border-opacity));
        }

        .border-gray-300 {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .border-gray-400 {
            --tw-border-opacity: 1;
            border-color: rgb(156 163 175 / var(--tw-border-opacity));
        }

        .border-gray-500 {
            --tw-border-opacity: 1;
            border-color: rgb(107 114 128 / var(--tw-border-opacity));
        }

        .border-green-700 {
            --tw-border-opacity: 1;
            border-color: rgb(21 128 61 / var(--tw-border-opacity));
        }

        .border-indigo-400 {
            --tw-border-opacity: 1;
            border-color: rgb(129 140 248 / var(--tw-border-opacity));
        }

        .border-red-700 {
            --tw-border-opacity: 1;
            border-color: rgb(185 28 28 / var(--tw-border-opacity));
        }

        .border-slate-300 {
            --tw-border-opacity: 1;
            border-color: rgb(203 213 225 / var(--tw-border-opacity));
        }

        .border-slate-500 {
            --tw-border-opacity: 1;
            border-color: rgb(100 116 139 / var(--tw-border-opacity));
        }

        .border-slate-600 {
            --tw-border-opacity: 1;
            border-color: rgb(71 85 105 / var(--tw-border-opacity));
        }

        .border-transparent {
            border-color: transparent;
        }

        .border-yellow-700 {
            --tw-border-opacity: 1;
            border-color: rgb(161 98 7 / var(--tw-border-opacity));
        }

        .bg-blue-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(59 130 246 / var(--tw-bg-opacity));
        }

        .bg-gray-100 {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity));
        }

        .bg-gray-200 {
            --tw-bg-opacity: 1;
            background-color: rgb(229 231 235 / var(--tw-bg-opacity));
        }

        .bg-gray-50 {
            --tw-bg-opacity: 1;
            background-color: rgb(249 250 251 / var(--tw-bg-opacity));
        }

        .bg-gray-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(107 114 128 / var(--tw-bg-opacity));
        }

        .bg-gray-800 {
            --tw-bg-opacity: 1;
            background-color: rgb(31 41 55 / var(--tw-bg-opacity));
        }

        .bg-green-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(34 197 94 / var(--tw-bg-opacity));
        }

        .bg-indigo-50 {
            --tw-bg-opacity: 1;
            background-color: rgb(238 242 255 / var(--tw-bg-opacity));
        }

        .bg-indigo-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(99 102 241 / var(--tw-bg-opacity));
        }

        .bg-indigo-600 {
            --tw-bg-opacity: 1;
            background-color: rgb(79 70 229 / var(--tw-bg-opacity));
        }

        .bg-red-100 {
            --tw-bg-opacity: 1;
            background-color: rgb(254 226 226 / var(--tw-bg-opacity));
        }

        .bg-red-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .bg-red-600 {
            --tw-bg-opacity: 1;
            background-color: rgb(220 38 38 / var(--tw-bg-opacity));
        }

        .bg-red-700 {
            --tw-bg-opacity: 1;
            background-color: rgb(185 28 28 / var(--tw-bg-opacity));
        }

        .bg-slate-200 {
            --tw-bg-opacity: 1;
            background-color: rgb(226 232 240 / var(--tw-bg-opacity));
        }

        .bg-slate-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(100 116 139 / var(--tw-bg-opacity));
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity));
        }

        .bg-yellow-500 {
            --tw-bg-opacity: 1;
            background-color: rgb(234 179 8 / var(--tw-bg-opacity));
        }

        .bg-opacity-25 {
            --tw-bg-opacity: 0.25;
        }

        .bg-cover {
            background-size: cover;
        }

        .bg-center {
            background-position: center;
        }

        .bg-no-repeat {
            background-repeat: no-repeat;
        }

        .fill-black {
            fill: #000;
        }

        .fill-indigo-500 {
            fill: #6366f1;
        }

        .stroke-gray-400 {
            stroke: #9ca3af;
        }

        .object-cover {
            -o-object-fit: cover;
            object-fit: cover;
        }

        .p-1 {
            padding: 0.25rem;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .px-1 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .py-10 {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        .py-12 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-5 {
            padding-top: 1.25rem;
            padding-bottom: 1.25rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .py-8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .pb-1 {
            padding-bottom: 0.25rem;
        }

        .pb-3 {
            padding-bottom: 0.75rem;
        }

        .pb-4 {
            padding-bottom: 1rem;
        }

        .pe-4 {
            padding-inline-end: 1rem;
        }

        .pl-2 {
            padding-left: 0.5rem;
        }

        .ps-3 {
            padding-inline-start: 0.75rem;
        }

        .pt-0 {
            padding-top: 0px;
        }

        .pt-0\.5 {
            padding-top: 0.125rem;
        }

        .pt-1 {
            padding-top: 0.25rem;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .pt-5 {
            padding-top: 1.25rem;
        }

        .pt-6 {
            padding-top: 1.5rem;
        }

        .pt-8 {
            padding-top: 2rem;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        .text-start {
            text-align: start;
        }

        .text-end {
            text-align: end;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        .font-sans {
            font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .text-8xl {
            font-size: 6rem;
            line-height: 1;
        }

        .text-base {
            font-size: 1rem;
            line-height: 1.5rem;
        }

        .text-lg {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .text-xs {
            font-size: 0.75rem;
            line-height: 1rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-medium {
            font-weight: 500;
        }

        .font-semibold {
            font-weight: 600;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .italic {
            font-style: italic;
        }

        .leading-4 {
            line-height: 1rem;
        }

        .leading-5 {
            line-height: 1.25rem;
        }

        .leading-7 {
            line-height: 1.75rem;
        }

        .leading-relaxed {
            line-height: 1.625;
        }

        .leading-tight {
            line-height: 1.25;
        }

        .tracking-wider {
            letter-spacing: 0.05em;
        }

        .tracking-widest {
            letter-spacing: 0.1em;
        }

        .text-black {
            --tw-text-opacity: 1;
            color: rgb(0 0 0 / var(--tw-text-opacity));
        }

        .text-blue-500 {
            --tw-text-opacity: 1;
            color: rgb(59 130 246 / var(--tw-text-opacity));
        }

        .text-gray-200 {
            --tw-text-opacity: 1;
            color: rgb(229 231 235 / var(--tw-text-opacity));
        }

        .text-gray-300 {
            --tw-text-opacity: 1;
            color: rgb(209 213 219 / var(--tw-text-opacity));
        }

        .text-gray-400 {
            --tw-text-opacity: 1;
            color: rgb(156 163 175 / var(--tw-text-opacity));
        }

        .text-gray-500 {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity));
        }

        .text-gray-700 {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }

        .text-gray-800 {
            --tw-text-opacity: 1;
            color: rgb(31 41 55 / var(--tw-text-opacity));
        }

        .text-gray-900 {
            --tw-text-opacity: 1;
            color: rgb(17 24 39 / var(--tw-text-opacity));
        }

        .text-green-400 {
            --tw-text-opacity: 1;
            color: rgb(74 222 128 / var(--tw-text-opacity));
        }

        .text-green-500 {
            --tw-text-opacity: 1;
            color: rgb(34 197 94 / var(--tw-text-opacity));
        }

        .text-green-600 {
            --tw-text-opacity: 1;
            color: rgb(22 163 74 / var(--tw-text-opacity));
        }

        .text-indigo-600 {
            --tw-text-opacity: 1;
            color: rgb(79 70 229 / var(--tw-text-opacity));
        }

        .text-indigo-700 {
            --tw-text-opacity: 1;
            color: rgb(67 56 202 / var(--tw-text-opacity));
        }

        .text-red-500 {
            --tw-text-opacity: 1;
            color: rgb(239 68 68 / var(--tw-text-opacity));
        }

        .text-red-600 {
            --tw-text-opacity: 1;
            color: rgb(220 38 38 / var(--tw-text-opacity));
        }

        .text-white {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }

        .text-yellow-500 {
            --tw-text-opacity: 1;
            color: rgb(234 179 8 / var(--tw-text-opacity));
        }

        .underline {
            text-decoration-line: underline;
        }

        .antialiased {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .opacity-0 {
            opacity: 0;
        }

        .opacity-100 {
            opacity: 1;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        .opacity-75 {
            opacity: 0.75;
        }

        .shadow {
            --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .shadow-lg {
            --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .shadow-md {
            --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .shadow-sm {
            --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .shadow-xl {
            --tw-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .shadow-gray-200 {
            --tw-shadow-color: #e5e7eb;
            --tw-shadow: var(--tw-shadow-colored);
        }

        .ring-1 {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .ring-2 {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .ring-black {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(0 0 0 / var(--tw-ring-opacity));
        }

        .ring-blue-500 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(59 130 246 / var(--tw-ring-opacity));
        }

        .ring-gray-300 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(209 213 219 / var(--tw-ring-opacity));
        }

        .ring-green-500 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(34 197 94 / var(--tw-ring-opacity));
        }

        .ring-red-500 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(239 68 68 / var(--tw-ring-opacity));
        }

        .ring-yellow-500 {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(234 179 8 / var(--tw-ring-opacity));
        }

        .ring-opacity-5 {
            --tw-ring-opacity: 0.05;
        }

        .ring-opacity-50 {
            --tw-ring-opacity: 0.5;
        }

        .transition {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-backdrop-filter;
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .transition-transform {
            transition-property: transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        .duration-100 {
            transition-duration: 100ms;
        }

        .duration-150 {
            transition-duration: 150ms;
        }

        .duration-200 {
            transition-duration: 200ms;
        }

        .duration-300 {
            transition-duration: 300ms;
        }

        .duration-75 {
            transition-duration: 75ms;
        }

        .ease-in {
            transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
        }

        .ease-in-out {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ease-out {
            transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }

        [x-cloak] {
            display: none;
        }

        @media (prefers-color-scheme: dark) {
            .dark\:prose-invert {
                --tw-prose-body: var(--tw-prose-invert-body);
                --tw-prose-headings: var(--tw-prose-invert-headings);
                --tw-prose-lead: var(--tw-prose-invert-lead);
                --tw-prose-links: var(--tw-prose-invert-links);
                --tw-prose-bold: var(--tw-prose-invert-bold);
                --tw-prose-counters: var(--tw-prose-invert-counters);
                --tw-prose-bullets: var(--tw-prose-invert-bullets);
                --tw-prose-hr: var(--tw-prose-invert-hr);
                --tw-prose-quotes: var(--tw-prose-invert-quotes);
                --tw-prose-quote-borders: var(--tw-prose-invert-quote-borders);
                --tw-prose-captions: var(--tw-prose-invert-captions);
                --tw-prose-kbd: var(--tw-prose-invert-kbd);
                --tw-prose-kbd-shadows: var(--tw-prose-invert-kbd-shadows);
                --tw-prose-code: var(--tw-prose-invert-code);
                --tw-prose-pre-code: var(--tw-prose-invert-pre-code);
                --tw-prose-pre-bg: var(--tw-prose-invert-pre-bg);
                --tw-prose-th-borders: var(--tw-prose-invert-th-borders);
                --tw-prose-td-borders: var(--tw-prose-invert-td-borders);
            }
        }

        .selection\:bg-red-500 *::-moz-selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .selection\:bg-red-500 *::selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .selection\:text-white *::-moz-selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }

        .selection\:text-white *::selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }

        .selection\:bg-red-500::-moz-selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .selection\:bg-red-500::selection {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .selection\:text-white::-moz-selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }

        .selection\:text-white::selection {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }

        .last\:border-b:last-child {
            border-bottom-width: 1px;
        }

        .hover\:border-gray-300:hover {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .hover\:bg-blue-600:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(37 99 235 / var(--tw-bg-opacity));
        }

        .hover\:bg-gray-100:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity));
        }

        .hover\:bg-gray-50:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(249 250 251 / var(--tw-bg-opacity));
        }

        .hover\:bg-gray-700:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(55 65 81 / var(--tw-bg-opacity));
        }

        .hover\:bg-indigo-600:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(79 70 229 / var(--tw-bg-opacity));
        }

        .hover\:bg-red-500:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(239 68 68 / var(--tw-bg-opacity));
        }

        .hover\:bg-red-600:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(220 38 38 / var(--tw-bg-opacity));
        }

        .hover\:bg-slate-300:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(203 213 225 / var(--tw-bg-opacity));
        }

        .hover\:text-gray-400:hover {
            --tw-text-opacity: 1;
            color: rgb(156 163 175 / var(--tw-text-opacity));
        }

        .hover\:text-gray-500:hover {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .hover\:text-gray-700:hover {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }

        .hover\:text-gray-800:hover {
            --tw-text-opacity: 1;
            color: rgb(31 41 55 / var(--tw-text-opacity));
        }

        .hover\:text-gray-900:hover {
            --tw-text-opacity: 1;
            color: rgb(17 24 39 / var(--tw-text-opacity));
        }

        .hover\:text-red-600:hover {
            --tw-text-opacity: 1;
            color: rgb(220 38 38 / var(--tw-text-opacity));
        }

        .focus\:z-10:focus {
            z-index: 10;
        }

        .focus\:rounded-sm:focus {
            border-radius: 0.125rem;
        }

        .focus\:border-none:focus {
            border-style: none;
        }

        .focus\:border-blue-300:focus {
            --tw-border-opacity: 1;
            border-color: rgb(147 197 253 / var(--tw-border-opacity));
        }

        .focus\:border-gray-300:focus {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .focus\:border-indigo-500:focus {
            --tw-border-opacity: 1;
            border-color: rgb(99 102 241 / var(--tw-border-opacity));
        }

        .focus\:border-indigo-700:focus {
            --tw-border-opacity: 1;
            border-color: rgb(67 56 202 / var(--tw-border-opacity));
        }

        .focus\:bg-gray-100:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity));
        }

        .focus\:bg-gray-50:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(249 250 251 / var(--tw-bg-opacity));
        }

        .focus\:bg-gray-700:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(55 65 81 / var(--tw-bg-opacity));
        }

        .focus\:bg-indigo-100:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(224 231 255 / var(--tw-bg-opacity));
        }

        .focus\:bg-indigo-600:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(79 70 229 / var(--tw-bg-opacity));
        }

        .focus\:bg-red-600:focus {
            --tw-bg-opacity: 1;
            background-color: rgb(220 38 38 / var(--tw-bg-opacity));
        }

        .focus\:text-gray-500:focus {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .focus\:text-gray-700:focus {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }

        .focus\:text-gray-800:focus {
            --tw-text-opacity: 1;
            color: rgb(31 41 55 / var(--tw-text-opacity));
        }

        .focus\:text-indigo-800:focus {
            --tw-text-opacity: 1;
            color: rgb(55 48 163 / var(--tw-text-opacity));
        }

        .focus\:outline-none:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .focus\:outline:focus {
            outline-style: solid;
        }

        .focus\:outline-2:focus {
            outline-width: 2px;
        }

        .focus\:outline-red-500:focus {
            outline-color: #ef4444;
        }

        .focus\:ring:focus {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .focus\:ring-2:focus {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .focus\:ring-indigo-500:focus {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(99 102 241 / var(--tw-ring-opacity));
        }

        .focus\:ring-red-500:focus {
            --tw-ring-opacity: 1;
            --tw-ring-color: rgb(239 68 68 / var(--tw-ring-opacity));
        }

        .focus\:ring-offset-2:focus {
            --tw-ring-offset-width: 2px;
        }

        .active\:bg-gray-100:active {
            --tw-bg-opacity: 1;
            background-color: rgb(243 244 246 / var(--tw-bg-opacity));
        }

        .active\:bg-gray-50:active {
            --tw-bg-opacity: 1;
            background-color: rgb(249 250 251 / var(--tw-bg-opacity));
        }

        .active\:bg-gray-900:active {
            --tw-bg-opacity: 1;
            background-color: rgb(17 24 39 / var(--tw-bg-opacity));
        }

        .active\:bg-red-700:active {
            --tw-bg-opacity: 1;
            background-color: rgb(185 28 28 / var(--tw-bg-opacity));
        }

        .active\:text-gray-500:active {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .active\:text-gray-700:active {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }

        .disabled\:opacity-25:disabled {
            opacity: 0.25;
        }

        :is([dir="ltr"] .ltr\:origin-top-left) {
            transform-origin: top left;
        }

        :is([dir="ltr"] .ltr\:origin-top-right) {
            transform-origin: top right;
        }

        :is([dir="rtl"] .rtl\:origin-top-left) {
            transform-origin: top left;
        }

        :is([dir="rtl"] .rtl\:origin-top-right) {
            transform-origin: top right;
        }

        @media (prefers-color-scheme: dark) {
            .dark\:border-gray-500 {
                --tw-border-opacity: 1;
                border-color: rgb(107 114 128 / var(--tw-border-opacity));
            }

            .dark\:border-gray-600 {
                --tw-border-opacity: 1;
                border-color: rgb(75 85 99 / var(--tw-border-opacity));
            }

            .dark\:border-gray-700 {
                --tw-border-opacity: 1;
                border-color: rgb(55 65 81 / var(--tw-border-opacity));
            }

            .dark\:border-indigo-600 {
                --tw-border-opacity: 1;
                border-color: rgb(79 70 229 / var(--tw-border-opacity));
            }

            .dark\:border-slate-500 {
                --tw-border-opacity: 1;
                border-color: rgb(100 116 139 / var(--tw-border-opacity));
            }

            .dark\:bg-gray-200 {
                --tw-bg-opacity: 1;
                background-color: rgb(229 231 235 / var(--tw-bg-opacity));
            }

            .dark\:bg-gray-400 {
                --tw-bg-opacity: 1;
                background-color: rgb(156 163 175 / var(--tw-bg-opacity));
            }

            .dark\:bg-gray-700 {
                --tw-bg-opacity: 1;
                background-color: rgb(55 65 81 / var(--tw-bg-opacity));
            }

            .dark\:bg-gray-800 {
                --tw-bg-opacity: 1;
                background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            }

            .dark\:bg-gray-900 {
                --tw-bg-opacity: 1;
                background-color: rgb(17 24 39 / var(--tw-bg-opacity));
            }

            .dark\:bg-indigo-900\/50 {
                background-color: rgb(49 46 129 / 0.5);
            }

            .dark\:bg-slate-400 {
                --tw-bg-opacity: 1;
                background-color: rgb(148 163 184 / var(--tw-bg-opacity));
            }

            .dark\:bg-slate-700 {
                --tw-bg-opacity: 1;
                background-color: rgb(51 65 85 / var(--tw-bg-opacity));
            }

            .dark\:bg-gradient-to-bl {
                background-image: linear-gradient(to bottom left, var(--tw-gradient-stops));
            }

            .dark\:from-gray-700\/50 {
                --tw-gradient-from: rgb(55 65 81 / 0.5) var(--tw-gradient-from-position);
                --tw-gradient-to: rgb(55 65 81 / 0) var(--tw-gradient-to-position);
                --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
            }

            .dark\:via-transparent {
                --tw-gradient-to: rgb(0 0 0 / 0) var(--tw-gradient-to-position);
                --tw-gradient-stops: var(--tw-gradient-from), transparent var(--tw-gradient-via-position), var(--tw-gradient-to);
            }

            .dark\:fill-indigo-200 {
                fill: #c7d2fe;
            }

            .dark\:fill-white {
                fill: #fff;
            }

            .dark\:text-gray-100 {
                --tw-text-opacity: 1;
                color: rgb(243 244 246 / var(--tw-text-opacity));
            }

            .dark\:text-gray-200 {
                --tw-text-opacity: 1;
                color: rgb(229 231 235 / var(--tw-text-opacity));
            }

            .dark\:text-gray-300 {
                --tw-text-opacity: 1;
                color: rgb(209 213 219 / var(--tw-text-opacity));
            }

            .dark\:text-gray-400 {
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity));
            }

            .dark\:text-gray-500 {
                --tw-text-opacity: 1;
                color: rgb(107 114 128 / var(--tw-text-opacity));
            }

            .dark\:text-gray-800 {
                --tw-text-opacity: 1;
                color: rgb(31 41 55 / var(--tw-text-opacity));
            }

            .dark\:text-green-400 {
                --tw-text-opacity: 1;
                color: rgb(74 222 128 / var(--tw-text-opacity));
            }

            .dark\:text-indigo-300 {
                --tw-text-opacity: 1;
                color: rgb(165 180 252 / var(--tw-text-opacity));
            }

            .dark\:text-red-400 {
                --tw-text-opacity: 1;
                color: rgb(248 113 113 / var(--tw-text-opacity));
            }

            .dark\:text-red-600 {
                --tw-text-opacity: 1;
                color: rgb(220 38 38 / var(--tw-text-opacity));
            }

            .dark\:text-white {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity));
            }

            .dark\:hover\:border-gray-600:hover {
                --tw-border-opacity: 1;
                border-color: rgb(75 85 99 / var(--tw-border-opacity));
            }

            .dark\:hover\:border-gray-700:hover {
                --tw-border-opacity: 1;
                border-color: rgb(55 65 81 / var(--tw-border-opacity));
            }

            .dark\:hover\:bg-gray-500:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(107 114 128 / var(--tw-bg-opacity));
            }

            .dark\:hover\:bg-gray-700:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(55 65 81 / var(--tw-bg-opacity));
            }

            .dark\:hover\:bg-gray-800:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            }

            .dark\:hover\:bg-gray-900:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(17 24 39 / var(--tw-bg-opacity));
            }

            .dark\:hover\:bg-slate-500:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(100 116 139 / var(--tw-bg-opacity));
            }

            .dark\:hover\:bg-white:hover {
                --tw-bg-opacity: 1;
                background-color: rgb(255 255 255 / var(--tw-bg-opacity));
            }

            .dark\:hover\:text-gray-100:hover {
                --tw-text-opacity: 1;
                color: rgb(243 244 246 / var(--tw-text-opacity));
            }

            .dark\:hover\:text-gray-200:hover {
                --tw-text-opacity: 1;
                color: rgb(229 231 235 / var(--tw-text-opacity));
            }

            .dark\:hover\:text-gray-300:hover {
                --tw-text-opacity: 1;
                color: rgb(209 213 219 / var(--tw-text-opacity));
            }

            .dark\:hover\:text-gray-400:hover {
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity));
            }

            .dark\:hover\:text-slate-100:hover {
                --tw-text-opacity: 1;
                color: rgb(241 245 249 / var(--tw-text-opacity));
            }

            .dark\:hover\:text-white:hover {
                --tw-text-opacity: 1;
                color: rgb(255 255 255 / var(--tw-text-opacity));
            }

            .hover\:dark\:text-red-500:hover {
                --tw-text-opacity: 1;
                color: rgb(239 68 68 / var(--tw-text-opacity));
            }

            .dark\:focus\:border-gray-600:focus {
                --tw-border-opacity: 1;
                border-color: rgb(75 85 99 / var(--tw-border-opacity));
            }

            .dark\:focus\:border-gray-700:focus {
                --tw-border-opacity: 1;
                border-color: rgb(55 65 81 / var(--tw-border-opacity));
            }

            .dark\:focus\:border-indigo-300:focus {
                --tw-border-opacity: 1;
                border-color: rgb(165 180 252 / var(--tw-border-opacity));
            }

            .dark\:focus\:border-indigo-600:focus {
                --tw-border-opacity: 1;
                border-color: rgb(79 70 229 / var(--tw-border-opacity));
            }

            .dark\:focus\:bg-gray-700:focus {
                --tw-bg-opacity: 1;
                background-color: rgb(55 65 81 / var(--tw-bg-opacity));
            }

            .dark\:focus\:bg-gray-800:focus {
                --tw-bg-opacity: 1;
                background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            }

            .dark\:focus\:bg-gray-900:focus {
                --tw-bg-opacity: 1;
                background-color: rgb(17 24 39 / var(--tw-bg-opacity));
            }

            .dark\:focus\:bg-indigo-900:focus {
                --tw-bg-opacity: 1;
                background-color: rgb(49 46 129 / var(--tw-bg-opacity));
            }

            .dark\:focus\:bg-white:focus {
                --tw-bg-opacity: 1;
                background-color: rgb(255 255 255 / var(--tw-bg-opacity));
            }

            .dark\:focus\:text-gray-200:focus {
                --tw-text-opacity: 1;
                color: rgb(229 231 235 / var(--tw-text-opacity));
            }

            .dark\:focus\:text-gray-300:focus {
                --tw-text-opacity: 1;
                color: rgb(209 213 219 / var(--tw-text-opacity));
            }

            .dark\:focus\:text-gray-400:focus {
                --tw-text-opacity: 1;
                color: rgb(156 163 175 / var(--tw-text-opacity));
            }

            .dark\:focus\:text-indigo-200:focus {
                --tw-text-opacity: 1;
                color: rgb(199 210 254 / var(--tw-text-opacity));
            }

            .dark\:focus\:ring-indigo-600:focus {
                --tw-ring-opacity: 1;
                --tw-ring-color: rgb(79 70 229 / var(--tw-ring-opacity));
            }

            .dark\:focus\:ring-offset-gray-800:focus {
                --tw-ring-offset-color: #1f2937;
            }

            .dark\:active\:bg-gray-300:active {
                --tw-bg-opacity: 1;
                background-color: rgb(209 213 219 / var(--tw-bg-opacity));
            }

            .dark\:active\:bg-gray-700:active {
                --tw-bg-opacity: 1;
                background-color: rgb(55 65 81 / var(--tw-bg-opacity));
            }
        }

        @media (min-width: 640px) {
            .sm\:fixed {
                position: fixed;
            }

            .sm\:right-0 {
                right: 0px;
            }

            .sm\:top-0 {
                top: 0px;
            }

            .sm\:col-span-4 {
                grid-column: span 4 / span 4;
            }

            .sm\:-my-px {
                margin-top: -1px;
                margin-bottom: -1px;
            }

            .sm\:mx-0 {
                margin-left: 0px;
                margin-right: 0px;
            }

            .sm\:mx-auto {
                margin-left: auto;
                margin-right: auto;
            }

            .sm\:-me-2 {
                margin-inline-end: -0.5rem;
            }

            .sm\:ms-10 {
                margin-inline-start: 2.5rem;
            }

            .sm\:ms-3 {
                margin-inline-start: 0.75rem;
            }

            .sm\:ms-4 {
                margin-inline-start: 1rem;
            }

            .sm\:ms-6 {
                margin-inline-start: 1.5rem;
            }

            .sm\:ms-7 {
                margin-inline-start: 1.75rem;
            }

            .sm\:mt-0 {
                margin-top: 0px;
            }

            .sm\:block {
                display: block;
            }

            .sm\:flex {
                display: flex;
            }

            .sm\:hidden {
                display: none;
            }

            .sm\:h-10 {
                height: 2.5rem;
            }

            .sm\:w-10 {
                width: 2.5rem;
            }

            .sm\:w-full {
                width: 100%;
            }

            .sm\:max-w-2xl {
                max-width: 42rem;
            }

            .sm\:max-w-lg {
                max-width: 32rem;
            }

            .sm\:max-w-md {
                max-width: 28rem;
            }

            .sm\:max-w-sm {
                max-width: 24rem;
            }

            .sm\:max-w-xl {
                max-width: 36rem;
            }

            .sm\:flex-1 {
                flex: 1 1 0%;
            }

            .sm\:translate-x-0 {
                --tw-translate-x: 0px;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .sm\:translate-x-2 {
                --tw-translate-x: 0.5rem;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .sm\:translate-y-0 {
                --tw-translate-y: 0px;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .sm\:scale-100 {
                --tw-scale-x: 1;
                --tw-scale-y: 1;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .sm\:scale-95 {
                --tw-scale-x: .95;
                --tw-scale-y: .95;
                transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
            }

            .sm\:items-start {
                align-items: flex-start;
            }

            .sm\:items-center {
                align-items: center;
            }

            .sm\:justify-start {
                justify-content: flex-start;
            }

            .sm\:justify-center {
                justify-content: center;
            }

            .sm\:justify-between {
                justify-content: space-between;
            }

            .sm\:rounded-lg {
                border-radius: 0.5rem;
            }

            .sm\:rounded-md {
                border-radius: 0.375rem;
            }

            .sm\:rounded-bl-md {
                border-bottom-left-radius: 0.375rem;
            }

            .sm\:rounded-br-md {
                border-bottom-right-radius: 0.375rem;
            }

            .sm\:rounded-tl-md {
                border-top-left-radius: 0.375rem;
            }

            .sm\:rounded-tr-md {
                border-top-right-radius: 0.375rem;
            }

            .sm\:p-6 {
                padding: 1.5rem;
            }

            .sm\:px-0 {
                padding-left: 0px;
                padding-right: 0px;
            }

            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            .sm\:pb-4 {
                padding-bottom: 1rem;
            }

            .sm\:pt-0 {
                padding-top: 0px;
            }

            .sm\:text-center {
                text-align: center;
            }

            .sm\:text-start {
                text-align: start;
            }

            .sm\:text-4xl {
                font-size: 2.25rem;
                line-height: 2.5rem;
            }
        }

        @media (min-width: 768px) {
            .md\:col-span-1 {
                grid-column: span 1 / span 1;
            }

            .md\:col-span-2 {
                grid-column: span 2 / span 2;
            }

            .md\:col-span-3 {
                grid-column: span 3 / span 3;
            }

            .md\:mt-0 {
                margin-top: 0px;
            }

            .md\:mt-9 {
                margin-top: 2.25rem;
            }

            .md\:inline {
                display: inline;
            }

            .md\:table-cell {
                display: table-cell;
            }

            .md\:grid {
                display: grid;
            }

            .md\:hidden {
                display: none;
            }

            .md\:w-2\/3 {
                width: 66.666667%;
            }

            .md\:w-4\/5 {
                width: 80%;
            }

            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .md\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .md\:items-center {
                align-items: center;
            }

            .md\:justify-center {
                justify-content: center;
            }

            .md\:gap-6 {
                gap: 1.5rem;
            }

            .md\:px-16 {
                padding-left: 4rem;
                padding-right: 4rem;
            }

            .md\:px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .md\:py-16 {
                padding-top: 4rem;
                padding-bottom: 4rem;
            }

            .md\:py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }

            .md\:text-8xl {
                font-size: 6rem;
                line-height: 1;
            }

            .md\:text-base {
                font-size: 1rem;
                line-height: 1.5rem;
            }

            .md\:text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }
        }

        @media (min-width: 1024px) {
            .lg\:col-span-4 {
                grid-column: span 4 / span 4;
            }

            .lg\:gap-8 {
                gap: 2rem;
            }

            .lg\:p-8 {
                padding: 2rem;
            }

            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
    </style>

    @livewireStyles
</head>

<body style="font-family: 'Arial', sans-serif;">
    {{-- <div class="watermark">
        <div class="watermark-text">{{ $applicationinfo->application_status }}</div>
    </div> --}}
    <table class="w-full">
        <tr>
            <td style="border-bottom: 1px solid gray;">
                <span class="font-bold text-xs my-auto">{{ __('Reference Number: ') }}</span><span
                    class="text-xs">{{ $applicationinfo->application_id }}</span>
            </td>
            <td style="text-align: right; border-bottom: 1px solid gray;">
                <span style="font-style: italic; color: red;"
                    class="text-xs font-bold">{{ __('(Please print on A4 Paper Size)') }}</span>
            </td>
        </tr>

        <tr>
            <td class="text-center pb-4" colspan="2">
                <span class="font-bold" style="font-size: 32px">{{ __('APLIKASYON SA PAGSAPI') }}</span>
            </td>
        </tr>

        <tr>
            <td style="width: 38%;"><b>{{ __('PETSA NG APLIKASYON:') }}</b></td>
            <td>{{ $applicationinfo->created_at->format('F j, Y') }}</td>
        </tr>

        <tr>
            <td class="text-center pt-4 italic" colspan="2">
                <span style="text-decoration: underline; font-size: 20px">{{ __('MGA PAGKAKAKILANLAN') }}</span>
            </td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('BUONG PANGALAN:') }}</b></td>
            <td class="pt-4">
                {{ $applicationinfo->lastname . ', ' . $applicationinfo->firstname . ' ' . $applicationinfo->middlename }}
            </td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('EDAD:') }}</b></td>
            <td class="pt-4">{{ $age . ' TAONG GULANG' }}</td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('ARAW NG KAPANGANAKAN:') }}</b></td>
            <td class="pt-4">{{ strtoupper(\Carbon\Carbon::parse($applicationinfo->birthday)->format('F j, Y')) }}
            </td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('LUGAR NG KAPANGANAKAN:') }}</b></td>
            <td class="pt-4">{{ $applicationinfo->birthplace }}</td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('TIRAHAN:') }}</b></td>
            <td class="pt-4">
                {{ $applicationinfo->address1 . ', ' . $applicationinfo->barangay . ', ' . $applicationinfo->municipality . ', ' . $applicationinfo->province }}
            </td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('KATAYUANG SIBIL:') }}</b></td>
            <td class="pt-4">{{ $applicationinfo->civil_status }}</td>
        </tr>

        <tr>
            <td class="pt-4" style="width: 38%;"><b>{{ __('BILANG NG KASAMBAHAY:') }}</b></td>
            <td class="pt-4">{{ $applicationinfo->family_members }}</td>
        </tr>

        <tr>
            <td class="" style="padding-top: 3rem; text-align: justify;" colspan="2">
                <p>{{ __('Magalang ko pong hinihiling na ako po ay tanggapin bilang kasapi ng ') . $powasinfo->zone . ' ' . $powasinfo->barangay . ' Potable Water System Project (' . $powasinfo->barangay . __(' POWAS ' . $powasinfo->phase . ').') }}
                </p>
                <p style="margin-top: 12px;">
                    {{ __('Ako po ay nakahandang magbayad ng halagang nararapat para sa naturang kahilingan.') }}</p>
                <p style="margin-top: 12px;">
                    {{ __('Buong puso ko pong tutuparin ang lahat ng mga patakaran at alintuntunin ng  ') . $powasinfo->barangay . __(' POWAS ') . $powasinfo->phase . __('.') }}
                </p>
                <p style="margin-top: 12px;">
                    {{ __('Ang anumang paglabag sa mga alituntunin ay magbibigay daan sa pamunuan na pansamantalang putulin ang serbisyo ng tubig o patawan ng karampatang kaparusahan ang sinumang lumabag sa itinakda ng mga may kapangyarihan.') }}
                </p>
            </td>
        </tr>
    </table>
    <table class="w-full" style="margin-top: 74px;">
        <tr>
            <td style="width: 47%; text-align: center;">
                <p>{{ $applicationinfo->lastname . ', ' . $applicationinfo->firstname . ' ' . $applicationinfo->middlename }}
                </p>
                <hr>
                <p style="font-size: 12px;">{{ __('Buong Pangalan at Pirma ng Aplikante') }}</p>
            </td>
            <td style="width: 6%;">

            </td>
            <td style="width: 47%;">

            </td>
        </tr>
        <tr>
            <td style="width: 47%;" style="padding-top: 32px;">
                <p>{{ __('INEREREKOMENDANG SANG-AYUNAN:') }}</p>
            </td>
            <td style="width: 6%;">

            </td>
            <td style="width: 47%;" style="padding-top: 32px;">
                <p>{{ __('SINASANG-AYUNAN:') }}</p>
            </td>
        </tr>
        <tr>
            <td style="width: 47%; text-align: center; padding-top: 56px;">
                <p>&nbsp;</p>
                <hr>
                <p style="font-size: 12px;">{{ __('Operations Manager') }}</p>
            </td>
            <td style="width: 6%;">

            </td>
            <td style="width: 47%; text-align: center; padding-top: 56px;">
                @isset($presidentinfo->userinfo)
                    <p>{{ $presidentinfo->userinfo->lastname . ', ' . $presidentinfo->userinfo->firstname . ' ' . $presidentinfo->userinfo->middlename }}
                    </p>
                @else
                    <p>&nbsp;</p>
                @endisset
                <hr>
                <p style="font-size: 12px;">{{ __('Chairman') }}</p>
            </td>
        </tr>
    </table>
    @livewireScripts
</body>

</html>
