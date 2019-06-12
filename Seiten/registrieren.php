<!DOCTYPE html>
<html>
    <head>
        <title>Registrieren</title>
        <meta charset="utf8" />
        <link rel="stylesheet" href="./css/navi-style.css"/>
        <link rel="stylesheet" href="./css/registrieren-style.css"/>
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
                        <h1>Registrieren</h1>
                    </div>
                    <div class="registrieren">
                        <div class="zeile">
                            <div>
                                <div class="text">Vorname</div>
                                <input class="linkesElement" size="24" maxtype="text" name="vorname" placeholder="Max">
                            </div>
                            <div>
                                <div class="text">Nachname</div>
                                <input size="24" maxtype="text" name="nachname" placeholder="Mustermann">
                            </div>
                        </div>
                        <div class="zeile">
                            <div>
                                <div class="text">Passwort</div>
                                <input class="linkesElement" size="25" type="password" name="passwort">
                            </div>
                            <div>
                                <div class="text">Passwort wiederholen</div>
                                <input size="25" type="password" name="passwortWiederholen">
                            </div>
                        </div>
                        <div class="zeile">
                            <div>
                                <div class="text">E-Mail</div>
                                <input class="linkesElement" size="24" maxtype="text" name="email" placeholder="M.M@informatik.hs-fulda.de">
                            </div>
                            <div>
                                <div class="text">Geburtsdatum</div>
                                <input size="24" maxtype="text" name="geburtsdatum" placeholder="09.09.1999">
                            </div>
                        </div>
                    </div>
                    <div class="buttonPosition">
                        <input class="button" type="submit" name="bestätigen" value="Bestätigen">
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