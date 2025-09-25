# QuizRush
Projekt zaliczeniowy - Aplikacje Internetowe
Proces instalacji (krok po kroku)
1. Wgrywanie plików
o Skopiuj wszystkie pliki aplikacji do katalogu publicznego na serwerze (np. przez
SFTP/WinSCP).
2. Uruchomienie instalatora
o Otwórz w przeglądarce adres: https://twojadomena.pl/quizrush_m/install.php
o Wypełnij formularz instalacyjny:
▪ Dane dostępowe do bazy danych (host, użytkownik, hasło, nazwa bazy)
o Instalator automatycznie utworzy strukturę bazy danych i zapisze konfigurację w
pliku db.php.
3. Uprawnienia katalogów
o Upewnij się, że katalogi uploads i avatars mają prawa do zapisu (chmod 755 lub 775).
4. Usunięcie instalatora
o Po zakończonej instalacji usuń plik install.php z serwera (ze względów bezpieczeństwa).
5. Uruchomienie aplikacji
o Otwórz stronę w przeglądarce (np. https://twojadomena.pl/index.php)
