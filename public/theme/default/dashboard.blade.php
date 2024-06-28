<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/theme/{{$theme}}/assets/components.chunk.css?v={{$version}}">
    <link rel="stylesheet" href="/theme/{{$theme}}/assets/umi.css?v={{$version}}">
    <link rel="stylesheet" href="/theme/{{$theme}}/assets/buithienban.css?v={{$version}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
       
    
    @if (file_exists(public_path("/theme/{$theme}/assets/custom.css")))
        <link rel="stylesheet" href="/theme/{{$theme}}/assets/custom.css?v={{$version}}">
    @endif
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
    @php ($colors = [
        'darkblue' => '#3b5998',
        'black' => '#343a40',
        'default' => '#0665d0',
        'green' => '#319795'
    ])
    <meta name="theme-color" content="{{$colors[$theme_config['theme_color']]}}">

    <title>{{ $domainTitle }}</title>
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700"> -->
    <script>
        window.domainTitle = @json($domainTitle);
    </script>
    <script>window.routerBase = "/";</script>
    <script>
        window.settings = {
            title: '{{ $domainTitle }}',
            
            assets_path: '/theme/{{$theme}}/assets',
            theme: {
                sidebar: '{{$theme_config['theme_sidebar']}}',
                header: '{{$theme_config['theme_header']}}',
                color: '{{$theme_config['theme_color']}}',
            },
            version: '{{$version}}',
            background_url: '{{$theme_config['background_url']}}',
            description: '{{$DTMota}}',
            i18n: [
                'zh-CN',
                'en-US',
                'ja-JP',
                'vi-VN',
                'ko-KR',
                'zh-TW',
                'fa-IR'
            ],
            logo: '{{$DTLogo}}'
        }
    </script>
    <script src="/theme/{{$theme}}/assets/i18n/zh-CN.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/zh-TW.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/en-US.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/ja-JP.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/vi-VN.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/ko-KR.js?v={{$version}}"></script>
    <script src="/theme/{{$theme}}/assets/i18n/fa-IR.js?v={{$version}}"></script>
</head>

<body>
<div id="root"></div>
{!! $theme_config['custom_html'] !!}
<script src="/theme/{{$theme}}/assets/vendors.async.js?v={{$version}}"></script>
<script src="/theme/{{$theme}}/assets/buithienban.js?v={{$version}}"></script>
<script src="/theme/{{$theme}}/assets/components.async.js?v={{$version}}"></script>
<script src="/theme/{{$theme}}/assets/umi.js?v={{$version}}"></script>

    <script src="/assets/assets/buithienban.js"></script>
    <script src="/assets/assets/fastvnteam.js"></script>
    
    <script src="/assets/assets/buithienbanlimo.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" rel="stylesheet">

@if (file_exists(public_path("/theme/{$theme}/assets/custom.js")))
    <script src="/theme/{{$theme}}/assets/custom.js?v={{$version}}"></script>
@endif

<script>
    var clipboard = new ClipboardJS('.btn');

    function alert_success() {
        Swal.fire({
            icon: 'success',
            title: 'Thông báo',
            text: 'Sao chép thành công',
            timer: 1000,
            timerProgressBar: true
        });
    }

    function updateCardStyle() {
      var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
      var styleElement = document.createElement('style');

      if (width < 1040) {
        styleElement.innerHTML = '.card { margin-top: 1rem; }';
      } else {
        styleElement.innerHTML = '.card { margin-top: 1rem; margin-left: 35%; width: 30%; }';
      }

      var oldStyleElement = document.getElementById('card-style');
      if (oldStyleElement) {
        oldStyleElement.parentNode.removeChild(oldStyleElement);
      }

      styleElement.id = 'card-style';
      document.getElementsByTagName('head')[0].appendChild(styleElement);
    }
    </script>


</body>

</html>
