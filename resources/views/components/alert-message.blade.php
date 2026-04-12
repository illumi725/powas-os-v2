@props(['on'])

<div x-data="{ shown: false, timeout: null, showMessage: '', position: '', messageType: '', darkTheme: window.matchMedia('(prefers-color-scheme: dark)').matches }"
    x-init="
        @this.on('{{ $on }}', (data) => {
            console.log(data);
            clearTimeout(timeout);
            shown = true;
            showMessage = data[0].message;
            position = data[0].position;
            messageType = data[0].messageType;
            timeout = setTimeout(() => {
                shown = false
            }, 4000);
        })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    x-bind:class="{
        'fixed': true,
        'bottom-4': position === 'bottom-right' || position === 'bottom-left' || position === 'bottom-center',
        'top-4': position === 'top-right' || position === 'top-left' || position === 'top-center',
        'right-4': position === 'bottom-right' || position === 'top-right',
        'left-4': position === 'bottom-left' || position === 'top-left',
        'left-1/2 transform -translate-x-1/2': position === 'bottom-center' || position === 'top-center',
        'alert': true,
        'dark-mode': darkTheme,
        'alert-success': messageType === 'success',
        'alert-warning': messageType === 'warning',
        'alert-danger': messageType === 'error',
        'alert-info': messageType === 'info',
        'alert-success-dark': darkTheme && messageType === 'success',
        'alert-warning-dark': darkTheme && messageType === 'warning',
        'alert-danger-dark': darkTheme && messageType === 'error',
        'alert-info-dark': darkTheme && messageType === 'info',
    }"
    style="display: none; z-index: 1000;"
>
    <div>
        <div class="flex items-center">
            <span x-html="getIcon(messageType)" class="mr-2"></span>
            <p x-text="showMessage"></p>
        </div>
    </div>
</div>

<script>
    function getIcon(messageType) {
        switch (messageType) {
            case 'success':
                return '<i class="fa-regular fa-circle-check"></i>';
            case 'warning':
                return '<i class="fa-solid fa-triangle-exclamation"></i>';
            case 'error':
                return '<i class="fa-solid fa-circle-xmark"></i>';
            case 'info':
                return '<i class="fa-solid fa-circle-info"></i>';
            default:
                return '';
        }
    }
</script>
