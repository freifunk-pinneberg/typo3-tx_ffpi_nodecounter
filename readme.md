Freifunk Nodecounter Extension für TYPO3 CMS
=============================================

Diese Extension zählt die Knoten einer Freifunk Community anhand der ausgabe von [ffmap-backend](https://github.com/ffnord/ffmap-backend).
Im Frontend lässt sich dann folgendes anzeigen.

 - Gesammtzhal der Knoten
 - Anzahl der online Knoten
 - Anzahl der offline Knoten
 - Anzahl der verbundenen User

Im Typoscript muss der Pfad zur nodes.json angeben werden. Dabei kann es sich sowohl um eine Lokal Datei, als auch um eine externe HTTP Resource handeln. Ein Typoscript Beispiel liegt in `ext_typoscript_setup.txt`
Die ausgabe kann mit einem HTML Template angepasst werden. Ein beispiel liegt unter `pi1/templates/counter.html`


Dieses Plugin wurde ursprünglich für [Freifunk Pinneberg](https://pinneberg.freifunk.net) entwickelt.
Der Quellcode steht unter der GNU General Public License