function va_loader(a, b, c) {
  var d = document.createElement(a);
  d.type = (a === 'script') ? 'text/javascript' : 'text/css';
  if(b.indexOf('http') != -1 && b.indexOf('.js') != -1) d.src = b;
  else if(b.indexOf('http') != -1 && b.indexOf('.css') != -1) {
    d.rel = 'stylesheet';
    d.href = b;
  } else d.innerHTML = b;
  //console.log(b);
  for(var e in document.getElementsByTagName('head')[0].children) {
    var f = document.getElementsByTagName('head')[0].children[e];
    if(f.tagName === 'SCRIPT' || f.tagName === 'LINK') {
      if((f.src && f.src.indexOf(c) != -1) || (f.href && f.href.indexOf(c) != -1)) {
        document.getElementsByTagName('head')[0].insertBefore(d, f.nextSibling);
      }
    }
  }
}

/* Can be used like so 
va_loader('script', 'http://jquery-ui.googlecode.com/svn/tags/latest/ui/minified/jquery-ui.min.js', 'jquery.min.js');
va_loader('style', 'http://localhost/test.css', 'somestylesheet.css');
va_loader('script', 'var my_js_option = \'hello\';alert(my_js_option);', 'jquery.min.js');
va_loader('style', 'body {background: #000;}', 'somestylesheet.css');
*/