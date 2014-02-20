var JCommentsSocialLogin = JCommentsSocialLogin || {
    _window:null,

    initialize:function () {
        var container = document.getElementById('jcomments-slogin-buttons');
        if (container !== null) {
            var elements = container.getElementsByTagName('a');
            for (var i = 0; i < elements.length; i++) {
                elements[i].onclick = function (e) {
                    if (typeof(JCommentsSocialLogin._window) == 'window') {
                        JCommentsSocialLogin._window.close();
                    }
                    var spans = this.getElementsByTagName('span');
                    if (spans.length) {
                        var type = spans[0].className;
                        var params = JCommentsSocialLogin.getPopupParams(type);
                        JCommentsSocialLogin._window = window.open(this.href, 'LoginPopUp', params);
                        JCommentsSocialLogin._window.focus();
                    }
                    return false;
                }
            }
        }
    },

    getWindowSize:function () {
        var size = {width:0, height:0};
        if (typeof(window.innerWidth) == 'number') {
            // Non-IE
            size = {width:window.innerWidth, height:window.innerHeight};
        } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            // IE 6+ in 'standards compliant mode'
            size = {width:document.documentElement.clientWidth, height:document.documentElement.clientHeight};
        } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
            // IE 4 compatible
            size = {width:document.body.clientWidth, height:document.body.clientHeight};
        }
        return size;
    },

    getPopupParams:function (type) {
        var parameters = 'resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes';
        var size = {width:0, height:0};
        switch (type) {
            case 'vkontakte':
                size = {width:585, height:350};
                break;
            case 'google':
                size = {width:650, height:450};
                break;
            case 'facebook':
            case 'twitter':
            default:
                size = {width:900, height:550};
                break;
        }
        if (size.width > 0) {
            var windowSize = JCommentsSocialLogin.getWindowSize();
            var windowTop = (windowSize.height - size.height) / 2;
            var windowLeft = (windowSize.width - size.width) / 2;
            parameters += ',width=' + size.width + ',height=' + size.height + ',top=' + windowTop + ',left=' + windowLeft;
        }
        return parameters;
    },

    addListener:function (obj, type, listener) {
        if (obj.addEventListener) {
            obj.addEventListener(type, listener, false);
            return true;
        } else if (obj.attachEvent) {
            obj.attachEvent('on' + type, listener);
            return true;
        }
        return false;
    }
};

JCommentsSocialLogin.addListener(window, 'load', function () {
    JCommentsSocialLogin.initialize();
});