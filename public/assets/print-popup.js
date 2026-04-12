let printWindow;

function openPopup(element) {
    var url = document.getElementById(element).getAttribute("href");
    var windowWidth = 960;
    var windowHeight = 640;

    console.log(url);

    var screenWidth = window.screen.width;
    var screenHeight = window.screen.height;

    var leftPosition = (screenWidth - windowWidth) / 2;
    var topPosition = (screenHeight - windowHeight) / 2;

    var windowFeatures =
        "width=" +
        windowWidth +
        ",height=" +
        windowHeight +
        ",left=" +
        leftPosition +
        ",top=" +
        topPosition +
        ",resizable=0";

    console.log(element);

    printWindow = window.open(url, "myPopup", windowFeatures);

    // if (element !== "collectionSheet" && element !== "readingSheet") {
    //     printWindow.print();
    // }

    // if (getMobileOperatingSystem() != "Android") {
    //     printWindow.addEventListener("afterprint", function () {
    //         printWindow.close();
    //     });
    // }

    return false;
}

function getMobileOperatingSystem() {
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;

    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }
    if (/android/i.test(userAgent)) {
        return "Android";
    }

    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    if (
        /Windows/.test(navigator.userAgent) &&
        !/Windows Phone|Windows Mobile/.test(navigator.userAgent)
    ) {
        return "Windows Desktop";
    }

    if (/Macintosh|MacIntel|MacPPC|Mac68K/.test(navigator.userAgent)) {
        return "MacOS";
    }

    if (/Linux/.test(navigator.userAgent) && !isAndroid) {
        return "Linux";
    }

    return "unknown";
}
