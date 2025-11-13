<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
        <style>
            /* cyrillic-ext */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
  unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* vietnamese */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* cyrillic-ext */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459WRhyzbi.woff2) format('woff2');
  unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
}
/* cyrillic */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459W1hyzbi.woff2) format('woff2');
  unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
/* vietnamese */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459WZhyzbi.woff2) format('woff2');
  unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
}
/* latin-ext */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459Wdhyzbi.woff2) format('woff2');
  unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Montserrat';
  font-style: normal;
  font-weight: 900;
  font-display: swap;
  src: url(/fonts.gstatic.com/s/montserrat/v26/JTUSjIg1_i6t8kCHKm459Wlhyw.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
* {
    -webkit-box-sizing: border-box;
    box-sizing: border-box
}

body {
    padding: 0;
    margin: 0
}

#notfound {
    position: relative;
    height: 100vh;
    background: #e4e4e4;
}

#notfound .notfound {
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
    transform: translate(-50%,-50%)
}

.notfound {
    max-width: 767px;
    width: 100%;
    line-height: 1.4;
    text-align: center
}

.notfound .notfound-404 {
    position: relative;
    height: 180px;
    margin-bottom: 20px;
    z-index: -1
}

.notfound .notfound-404 h1 {
    font-family: montserrat,sans-serif;
    position: absolute;
    left: 50%;
    top: 50%;
    -webkit-transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
    transform: translate(-50%,-50%);
    font-size: 224px;
    font-weight: 900;
    margin-top: 0;
    margin-bottom: 0;
    margin-left: -12px;
    color: #030005;
    text-transform: uppercase;
    text-shadow: -1px -1px 0 #8400ff,1px 1px 0 #ff005a;
    letter-spacing: -20px
}

.notfound .notfound-404 h2 {
    font-family: montserrat,sans-serif;
    position: absolute;
    left: 0;
    right: 0;
    top: 110px;
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    text-shadow: 0 2px 0 #8400ff;
    letter-spacing: 13px;
    margin: 0
}

.notfound a {
    font-family: montserrat,sans-serif;
    display: inline-block;
    text-transform: uppercase;
    color: #ff005a;
    text-decoration: none;
    border: 2px solid;
    background: 0 0;
    padding: 10px 40px;
    font-size: 14px;
    font-weight: 700;
    -webkit-transition: .2s all;
    transition: .2s all
}

.notfound a:hover {
    color: #8400ff
}

@media only screen and (max-width: 767px) {
    .notfound .notfound-404 h2 {
        font-size:24px
    }
}

@media only screen and (max-width: 480px) {
    .notfound .notfound-404 h1 {
        font-size:182px
    }
}

            * {
                position: relative;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
         
            .centered {
                position: relative;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .centered h2{
                /* position: absolute; */
                color: white;
                margin-bottom: 27px;
                font-family: 'Lato', sans-serif;
                font-size: 30px;
                top: 65%;
            }
            #notfound {
                position: relative;
                height: 100vh;
                background: #e4e4e4;
            }
            .notfound .notfound-404 {
                position: relative;
                height: 180px;
                margin-bottom: 194px;
                z-index: -1;
            }
            .notfound .notfound-404 h1 {
                font-family: montserrat, sans-serif;
                position: absolute;
                left: 50%;
                top: 0;
                -webkit-transform: translate(-50%, -50%);
                -ms-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
                font-size: 224px;
                font-weight: 900;
                margin-top: 0;
                margin-bottom: 0;
                margin-left: -12px;
                color: #1a1a1a;
                text-transform: uppercase;
                text-shadow: 0px 4px 7px #656565, 0px 6px 0 #000000;
                letter-spacing: -20px;
            }
           
            .notfound .notfound-404 h2 {
                font-family: montserrat, sans-serif;
                position: absolute;
                left: 0;
                right: 0;
                top: 74%;
                transform: translate(0, 50%);
                font-size: 2REM;
                font-weight: 700;
                color: #5d5d5d;
                text-transform: uppercase;
                text-shadow: 0 2px 3px #9f9f9f;
                letter-spacing: 4px;
                margin: 0;
            }
            
            .message {
            display: inline-block;
            line-height: 1.2;
            transition: line-height .2s, width .2s;
            overflow: hidden;
            }
            
            .message,
            .hidden {
            font-family: 'Roboto Slab', serif;
            font-size: 18px;
            }
            
            .hidden {
            color: #FFF;
            }
            
            /* CSS */
            .button-43 {
            background-image: linear-gradient(-180deg, #37AEE2 0%, #1E96C8 100%);
            border-radius: .5rem;
            box-sizing: border-box;
            color: #FFFFFF;
            display: flex;
            font-size: 16px;
            justify-content: center;
            padding: 1rem 1.75rem;
            text-decoration: none;
            width: 100%;
            border: 0;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            }
            
            .button-43:hover {
            background-image: linear-gradient(-180deg, #1D95C9 0%, #17759C 100%);
            }
            
            @media (min-width: 768px) {
            .button-43 {
                padding: 1rem 2rem;
            }
            }
        </style>
    </head>
    <body>
        <div id="notfound">
          <div class="notfound">
            <div class="notfound-404">
              <h1>@yield('code')</h1>
              <h2>@yield('message')</h2>
            </div>
            <a href="/dashboard">Dashboard</a>
          </div>
        </div>
    </body>
</html>

