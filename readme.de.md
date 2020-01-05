Freifunk Nodecounter Extension für TYPO3 CMS
=============================================

Diese Extension zählt die Knoten einer Freifunk Community anhand der ausgabe von [ffmap-backend](https://github.com/ffnord/ffmap-backend).
Im Frontend lässt sich dann folgendes anzeigen.

 - Gesammtzhal der Knoten
 - Anzahl der online Knoten
 - Anzahl der offline Knoten
 - Anzahl der verbundenen User

Im Typoscript muss der Pfad zur nodes.json angeben werden. Dabei kann es sich sowohl um eine Lokal Datei, als auch um eine externe http(s) Resource handeln. 
Beispiel:
```
plugin.tx_ffpinodecounter {
     settings.nodeListFile = http://meshviewer.pinneberg.freifunk.net/data/nodes.json
     settings.nodeListExternal = TRUE
 }
```

Die ausgabe kann mit einem Fluid Template angepasst werden. Die Template Pfade können per TypoScript gesetzt werden. `plugin.tx_ffpinodecounter_counter.view`

Der Counter ist auch über den PageType 2652017 verfügbar und kann so auch per ajax oder iframe aktualisiert werden.

Dieses Plugin wurde ursprünglich für [Freifunk Pinneberg](https://pinneberg.freifunk.net) entwickelt.
Der Quellcode steht unter der GNU General Public License