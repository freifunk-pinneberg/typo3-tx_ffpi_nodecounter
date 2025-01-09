Freifunk Nodecounter Extension for TYPO3 CMS
=============================================

This extension counts the nodes of a Freifunk community based on the output of [ffmap-backend](https://github.com/ffnord/ffmap-backend).
The following can then be displayed in the frontend.

 - Total nodes
 - Number of online nodes
 - Number of offline nodes
 - Number of connected users

The path to nodes.json must be specified in the Typoscript. This can be a local file or an external http(s) resource.
example:
```
plugin.tx_ffpinodecounter {
     settings.nodeListFile = http://meshviewer.pinneberg.freifunk.net/data/nodes.json
     settings.nodeListExternal = TRUE
 }
```

The output can be customized with a fluid template. The template paths can be set using TypoScript. `plugin.tx_ffpinodecounter_counter.view`

The counter is also available via PageType 2652017, and can therefore also be updated via ajax or iframe.

This plugin was originally written for [Freifunk Pinneberg](https://pinneberg.freifunk.net).
The source code is under the GNU General Public License.