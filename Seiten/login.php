<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <meta charset="utf8" />
        <link rel="stylesheet" href="./css/navi-style.css"/>
        <link rel="stylesheet" href="./css/login-style.css"/>
    </head>
    <body>
        <header>

            <div class="top-nav">
                <div class="logo">
                    <a href="#">
                        <img src="./img/logo-studyhome.png" height="130px" width="192px"/>
                    </a>
                </div>
                <div class="menu-top">
                    <a href="#">Vermieten</a>
                    <a href="#">Nachrichten</a>
                    <a href="#">Anmelden</a>
                    <a href="#">Mein Konto</a>
                    <a href="#">Abmelden</a>
                </div>
            </div>
        </header>
        <main>
            <border class="border">
                <form>
                    <div class="title">
                        <h1>Login</h1>
                    </div>
                    <div class="login">
                        <div class="text">E-Mail</div>
                        <input size="24" maxtype="text" name="email" placeholder="M.M@informatik.hs-fulda.de"><br><br>
                        <div class="text">Passwort</div>
                        <input size="25" type="password" name="passwort"><br><br>
                        <input class="button" type="submit" name="login" value="Login"><br>
                        <a href="#">Passwort vergessen?</a><br>
                    </div>
                    <div class="registrieren">
                        <div class="text">Neuer User</div>
                        <input class="button" type="submit" name="registrieren" value="Registrieren"><br>
                    </div>	
                </form>
            </border>
        </main>
        <footer>
            <div class="bottom-nav">
                <div class="menu-bottom">		
                    <a href="#">Über uns</a>
                    <a href="#">Impressum</a>
                    <a href="#">AGB</a>
                    <a href="#">Kontakt und Hilfe</a>
                </div>
            </div>
        </footer>
    </body>
</html>