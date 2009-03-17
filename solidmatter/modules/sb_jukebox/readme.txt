===========================
==      Genre Baum       ==
===========================

o Rock
    - Alternative Rock
        
    - Classic Rock
    
o Elektro
    - Trance
    - 

Blues music genres (4)
Country music genres (8)
Drum and bass subgenres (0)
Electronic music genres (19)
    Ambient music (6)
    Breakbeat (2)
    Breakcore (0)
    Drum and bass (6)
    Electroacoustic improvisation (0)
    Electronica (4)
    Eurodance (2)
    Folktronica (0)
    Futurepop (0)
    Grime artists (0)
    Hardcore music (3)
    House music (6)
    Industrial music (7)
    Noise music (7)
    Synthpop (5)
    Techno music (6)
    Trance music (6)
    UK garage (5)
    Video game music
Flamenco styles (0)
Folk music (20)
Funk genres (2)
Hardcore music genres (1)
Heavy metal subgenres (23)
Hip hop genres (16)
    Alternative hip hop (1)
    American hip hop genres (0)
    Christian hip hop (3)
    Detroit hip hop (2)
    East Coast hip hop (3)
    G-funk (1)
    Grime artists (0)
    Homo hop (0)
    Jewish hip hop (2)
    Nerdcore hip hop (2)
    Nu metal (1)
    Rap rock (8)
    Southern hip hop (2)
    Trip hop (3)
    Underground hip hop (0)
    West Coast hip hop (2)
House music genres (2)
    Ambient house
    Chicago house
    Dark house
    Deep house
    Dream house
    Electro house
    Freestyle music
    French house
    Ghetto house
    Hip house
    Kwaito
    Latin house
    Merenhouse
    Microhouse
    Nu-NRG
    Progressive dance music
    Skacid
    Tech house
    Tribal house
    Tribal record labels
    UK hard house
    Vocal house
Jazz genres (5)
Polka genres (0)
Pop music genres (18)
Reggae genres
    Dancehall
    Lovers rock
    Ragga
    Reggae genres
    Roots reggae
    Seggae
Rhythm and blues music genres (3)
Rock music genres (28)
Soul music genres (1)
Techno music genres (1)
    4-beat
    Acid techno
    Detroit techno
    Freetekno
    Ghettotech
    Hard trance
    Jtek
    Microhouse
    Minimal techno
    Nortec
    Rave music
    Rotterdam techno
    Schranz
    Speed Limit 140 BPM+
    Tech house
    Tech trance
    Techno folk
    Wonky techno
    Yorkshire Bleeps and
World music genres (2)


===========================
==   Wichtiger Hinweis   ==
===========================

Nur eine gut gepflegte Jukebox entfaltet den vollen Nutzen! Es nützt allen Beteiligten, wenn
- die Informationen in der Datenbank korrekt und vollständig sind
- die Benennung der Verzeichnisse und Dateien einheitlich ist
- Tags und Relationen vernünftig und umfassend gesetzt sind

Es mag nach viel Aufwand aussehen, den man treiben muss, wenn man sich dieses Dokument ansieht, aber letztendlich ist es eine Arbeit, die man sich nur einmal machen muss.
Und wenn man später die Musik schnell wiederfindet und sich nicht durch Kraut und Rüben kämpfen muss, wird man sehen dass es sich gelohnt hat.

==========================
==   Verzeichnisnamen   ==
==========================

    o Normale Alben:

       "Artist - [year] - Album"
           bspw: "No fun at all - [1996] - Out Of Bounds"

       "Artist - [year] - Album (Zusatzinfo)"

        Zusatzinfo bspw. Untertitel von Alben:
            "Apoptygma Berzerk - [2000] - Kathy's Song (Come Lie Next To Me)"
        

    o Für Soundtracks:
        "ST - Titel (Zusatzinfo) [year]"
        
    o Für Various Artists:
         "VA - Titel (Zusatzinfo) [year]"
             Zusatzinfo ist hier insbesondere: mixed by Artist
             
    o Für Various Artists/Compilations:

       Artist - year - Album [Typ] (Zusatzinfo)  
            CD1  
            CD2  
            CD3  
            CD1 - The Blue Lounge  
            CD2 - The Red Lounge  

    o Disksets, Alben mit mehreren CDs:
        Hier sollten, die einzelnen CDs in einem Unterverzeichnis liegen,
        
        - "Artist - [year] - Album"
        +- "CD1"
        +- "CD2"
             :
             :
        +- "CDn"
        +- folder.jpg

        Die Inhalte der einzelnen CD Unterordner folgen den oben genannten Regeln.




====================
==   Dateinamen   ==
====================

Die Dateinamen von MP3s SOLLTEN dem nachfolgend beschriebenen Schema entsprechen.
(die Angaben sind im MP3Tag-Format, können also ggf. in das Tool übernommen werden)

    - Für Alben von einem Artist...: %artist% - %album% - $num(%track%,2) - %title%
    - ...mit mehreren CDs: %artist% - %album% - $num(%discnumber%,1)-$num(%track%,2) - %title%
    
    - Für Various Artists / Soundtracks...: %album% - $num(%track%,2) - %artist% - %title%
    - ...mit mehreren CDs: %album% - $num(%discnumber%,1)-$num(%track%,2) - %artist% - %title%
    
HINWEIS: Prinzipiell ist das Format für die Dateinamen weniger wichtig, einzige Bedingung ist, dass die Reihenfolge auf der CD
auch die Reihenfolge im Verzeichniss sein MUSS, d.h. es muss eine Benennung gewählt werden, die dies erfüllt.
Ein einheitliches Schema hilft aber auch hier, Kraut und Rüben zu vermeiden, gerade wenn Alben oder Titel heruntergeladen werden.

Es kann vorkommen, dass bei langen Album- und Tracktiteln der Dateiname problematisch lang wird, in diesem Fall sollte er ggf. gekürzt werden.

==============
==   Tags   ==
==============

Tags können für ganze Alben schon vor dem Import in die Jukebox fix vergeben werden. 
Sie wirken sich dann auf das Album, die enthaltenen Tracks sowie den Artist aus.
Diese Infos werden im jeweiligen Album-verzeichnis in der Datei "sbTags.txt" mit Komma getrennt aufgelistet.
Auf diese Weise bleiben sie auf jeden Fall konserviert und stehen bei einem erneuten Import wieder zur Verfügung

Folgende Tags SOLLTEN IMMER angegeben werden, wenn sie auf das Album zutreffen:
- Special Edition
- Maxi ODER EP (Album mit wenigen, unterschiedlichen Titeln - http://de.wikipedia.org/wiki/Extended_Play)
- Single (Album ausschließlich mit Versionen von einem Titel - http://de.wikipedia.org/wiki/Single_(Musik))
- Deutsch (Deutsche Musik - sprachlich gesehen, nicht Herkunft der Band)
- incomplete (Wenn dem Album Titel fehlen, oder ein Titel nicht vollständig vorhanden ist)
- Best Of (Zusammenstellung mit Best-of Charakter)
- Compilation (sonstige Zusammenstellung von Titeln mehrerer Alben)
- Live (bei Live-Alben)
- Remix (wenn es sich um ein Album handelt das Remixes eines enderen enthält)
- Fake (wenn es sich angeblich um eine CD von einem Künstler handelt, das aber Bullshit ist)
        Beispiel: The Prodigy - The Castbreeder (ist eigentlich hauptsächlich Zusammenstellung von The Crystal Method) 
- Bootleg (bei unauthorisierten Zusammenstellungen)
        Beispiel: The Prodigy - The Rest, The Unreleased! THE LAST

===================
==   CHECKLIST   ==
===================

Diese Checkliste SOLLTE IMMER vor dem Hochladen eines neuen Albums durchgegangen werden!


  == Verzeichnisstruktur ==
  
  - es gibt nur ein Verzeichnis pro komplettem Album
  - dieses Verzeichnis ist "<Künstler> - [<Veröffentlichunsjahr>] - <Albumtitel>" benannt
  - einzelne CDs eines Albums sind als Unterordner abgebildet 
      - Die Verzeichnisse der CDs heißen "CD<Nummer>"
      - Sofern sie einen Untertitel besitzen, sind sie "CD<Nummer> - <Untertitel>" benannt
  - der Künstler ist bei Samplern u.Ä. "VA" oder "Various Artists", bei 
      Soundtracks "ST" oder "Soundtrack", bei sonstigen Zusammenstellungen "Mixed"
    
  == Mp3-Tags ==
  
  - folgende Tags sind vorhanden (und korrekt!) in ID3v1 und ID3v2 :
      - Künstler
      - Album
      - Titel
      - Tracknummer
      - Jahr
    - Genres (genaueres dazu später)
  - Der Künstler ist der Künstler des Tracks, nicht des Albums! Also nicht 
      "Various Artists" o.Ä.!
  - bei mehreren Künstlern ist der Künstler der Hauptkünstler sofern dieser 
      eindeutig ist, ansonsten werden alle genannt. Findet Anwendung bei Sachen wie
      "featuring XY" oder "remixed by XY". Beispiele:
      - "Cassius" - "Thrilla (feat. Ghostface Killa)" - NICHT "Cassius feat. Ghostface Killa" - "Thrilla", ansonsten ist der Track nicht dem richtigen Artist zugeordnet, bzw. die Anzahl Artists explodiert
      - "P.O.D." - "Boom (The Crystal Method Remix)"
      - "2 Double S vs. 2 Daniels" - "irgendein Track"
      - "Rockstar & La Cuard" - "irgendein Track"
  - die Tracknummer ist 2stellig und enthält Angaben über die Komplettzahl 
      der Titel (bspw. "03/18")
  - bei Alben mit mehreren CDs ist auch "CD in Set" korrekt gesetzt (bspw. "2/3")
  - bei Alben mit mehreren CDs hat der Albumname einen Zusatz und sieht etwa aus:
      - "<Album> (CD<Nummer>)" oder "<Album> (<Untertitel>)"
  - das Jahr ist das Erscheinungsjahr der CD
    
  == Covers ==
  
  - Covers sind sowohl im Verzeichnis als auch in den MP3s vorhanden
  - die Covers sind korrekt und haben eine gute Qualität(!)
  - das Hauptverzeichnis des Albums enthält eine Grafik des Covers, benannt "folder.jpg" (dann sieht man die Covers auch in der Windows-Thumbnail-Darstellung)
  - die Covergrafik im Verzeichnis hat:
      - eine Größe von idealerweise 600x600 aufwärts
      - nicht mehr als 2000x2000
      - nicht mehr als 1MB
  - die Covergrafik in den MP3s hat:
      - eine Größe von mindestens 300x300
      - höchstens 100kb (Größe hat Vorrang)
  
  == Genre Definitionen ==  
    
  - Überschneidungen/Mehrfachnennungen sind erlaubt und notwendig.
  - Mehrere Genres sind gemeinsam ins Tag Genre einzutragen, separiert durch " / "
  - Zuordnung zu Genres sind generell ein schwieriges Thema :-) folgende 
    Festlegungen sind zu beachten: 
    - Heavy Metal: nichts nach den 80ern - alles was man auf Kutten sehen kann  
      - Rock: alles mit E-Gitarren aber nicht schwer genug für Heavy Metal  
      - Pop: 90% aus den 80ern, generell Zeugs aus den Charts
 
 
 
 
 

