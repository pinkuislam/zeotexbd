<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <base href="{{ url('/') }}/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    {{-- facebook-meta-tag start --}}
    <meta name="facebook-domain-verification" content="tamzpt9ggg807z7yo4qi4os4oisfpg" />
    {{-- facebook-meta-tag end --}}

    <link rel="icon" type="image/x-icon" href="{{ $siteSettings->favicon_url ?? null }}">

    {{-- bootstrap@5.3.0 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />

    {{-- bootstrap-icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    {{-- font-awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- slick-carousel --}}
    <link rel="stylesheet" type="text/css" charset="UTF-8"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" />


    <link rel="stylesheet" href="{{ asset(mix('css/app.css', 'ecommerce-assets')) }}">

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KGSWC98K');
    </script>
    <!-- End Google Tag Manager -->

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-HHMD2E4NMB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-HHMD2E4NMB');
    </script>



    @inertiaHead
    @stack('styles')
</head>

<body>

    @inertia

    <script src="{{ asset(mix('js/inertia.js', 'ecommerce-assets')) }}"></script>

    {{-- bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>


    @stack('scripts')

    {{-- // Put this code snippet inside script tag live chat --}}
    <script>
        ! function() {
            var e = document.createElement("div");
            e.id = "myAliceWebChat";
            var t = document.createElement("script");
            t.type = "text/javascript", t.async = !0, t.src = "https://livechat.myalice.ai/index.js";
            var a = document.body.getElementsByTagName("script");
            (a = a[a.length - 1]).parentNode.insertBefore(t, a), a.parentNode.insertBefore(e, a), t.addEventListener("load",
                function() {
                    MyAliceWebChat.init({
                        selector: "#myAliceWebChat",
                        platformId: '17254',
                        primaryId: 'a99d182a9e4711eeaf74be7e83b4c5c7',
                        token: '0f8e374f6de50103c16dfeab44c2e6ecd8e3985b5b58a13b'
                    })
                })
        }();
    </script>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KGSWC98K" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

</body>

</html>
