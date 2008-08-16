= Richtlinien für Datei/Verzeichnis Namen =  
    
  == Allgemeines ==  
    
  Year sollte nach Möglichkeit das Erscheinungsjahr der CD (ggf. LP falls CD nur   
  Neuveröffentlichung) enthalten, keinesfalls pro Lied das Erstveröffenlichungsjahr,  
  sonst Probleme mit BestOf, Soundtracks und Co.  
    
  == Verzeichnisnamen ==  
    
  === Beispiele ===  
    
  Format HEIKO:  
         
       Artist - [year] - Album  
       Artist - [year] - Album - {128}  
         
         
  Format OLLO:  
    
       Artist - year - Album [Typ] (Zusatzinfo)  
            CD1  
            CD2  
            CDn  
            CDx - The Blue Lounge  
            CDy - The Red Lounge  
         
       Typ (optional) kann sein:  
            Single (bei Remixes von EINEM Lied)  
            Maxi (bei Remixes von mehreren Liedern)  
            Live (bei Live-Alben)  
            Fake (wenn angebliches Bootleg aber eigentlich andere Gruppe/n)  
         
       Zusatzinfo (optional) kann sein:  
            CDx (falls CDs aus einem Set fehlen, wird nur die vorhandene genannt)  
            128 (Bitrate falls beschissen)  
            bad quality (falls MP3s springen oder ähnlicher Rotz)  
            incomplete (falls Lieder fehlen)  
         
       Bei Compilations/Samplern auf jeden Fall 'VA' als Artist, bzw. 'ST' für Soundtracks  
         
    
  == Dateinamen ==  
         
       Index.Artist - Title.mp3  
       Artist - index - title.mp3  
         
       nicht erlaub sind:  
            101.adfsaf  
            102.adf  
             :  
            201.  
            202.  
    
    
  = CHECKLISTE =
  
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
  	- "Cassius" - "Thrilla (feat. Ghostface Killa)"
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
  - das Hauptverzeichnis des Albums enthält eine Grafik des Covers, benannt nach
  	einer der folgenden Alternativen:
  	- "front.jpg"
  	- "cover.jpg"
  	- "folder.jpg"
  	- alternativ die genannten Namen auch als .png
  - die Covergrafik im Verzeichnis hat eine Größe von 600x600, falls nicht 
  	möglich mindestens 300x300
  - die Covergrafik in den MP3s hat eine Größe von 300x300
  
  == Dateinamen ==
  
  - Die MP3s eines Albums sind alle einheitlich benannt nach folgendem Schema:
  	"<Künstler> - <Album> - <Tracknummer> - <Titel>.mp3"
  	
    
  == Genre Definitionen ==  
    
  - Überschneidungen/Mehrfachnennungen sind erlaubt und notwendig.
  - Mehrere Genres sind gemeinsam ins Tag Genre einzutragen, separiert durch " / "
  - Zuordnung zu Genres sind generell ein schwieriges Thema :-) folgende 
    Festlegungen sind zu beachten: 
    - Heavy Metal: nichts nach den 80ern - alles was man auf Kutten sehen kann  
  	- Rock: alles mit E-Gitarren aber nicht schwer genug für Heavy Metal  
  	- Pop: 90% aus den 80ern, generell Zeugs aus den Charts
    
  
  
  = Datenbank = 
  
  == Tabellen == 
  
  Die folgenden Tabellen sind notwendig: 
  
  -> sb_jukebox_artists 
      uuid               char(32)      
       s_name               varchar(100)      
  
  -> sb_jukebox_albums 
      uuid               char(32)                                     
       s_title               varchar(100)                               
       fk_artist          char(32)       
       n_published          smallint(4)      
       n_cdsinset          tinyint(4)      
       b_coverexists          enum('TRUE', 'FALSE')                                    
       s_coverfilename          varchar(50)                         
       s_path               varchar(250)      
       e_type               enum('DEFAULT', 'LIVE', 'FAKE', 'BOOTLEG', 'SINGLE', 'MAXI', 'BESTOF', 'TRIBUTE', 'REMIXES') 
       e_defects          enum('NONE', 'LOW_BITRATE', 'BAD_QUALITY', 'INCOMPLETE', 'CD_MISSING')      
  
  
  -> sb_jukebox_titles 
      uuid               char(32)      
       s_filename          varchar(200)      
       fk_artist          char(32)      
       fk_album          char(32)      
       s_title               varchar(100)      
       s_index               varchar(5)      
       n_published          smallint(4) 
  
  -> sb_jukebox_genres 
      uuid               char(32) 
       s_genre               varchar(50) 
  
  -> sb_jukebox_titles_genres 
      fk_title     char(32)      
       fk_genre     char(32) 
  
  -> sb_jukebox_votes 
      fk_user               char(32)      
       fk_jukeboxentity     char(32)      
       n_vote               tinyint(4) 
  
  -> sb_jukebox_blacklist 
      fk_user               char(32) 
       fk_subject          char(32) 
  
  
  
 = Solidmatter / Perl import =  
  
  
  
 rückgabewert: 
 album['artist'] ['exists'] 
                 ['name'] 
                 ['uuid'] 
  
 album['album']  ['exists'] 
                 ['name'] 
                 ['uuid'] 
  
 album['titles'] [0] ['exists'] 
                     ['name'] 
                     ['uuid'] 
                 [1] ['exists'] 
                     ['name'] 
                     ['uuid'] 
                  : 
                  : 
                 [n] ['exists'] 
                     ['name'] 
                     ['uuid'] 
  
  
 = Player = 
  
 um MP3 dateien abzuspielen stehen folgende Möglichkeiten zur Verfügung: 
  
 streaming 
 - vlc 
 - icecast 
  
 - java applet plugin 
  
 - http access (paththrouhg) 
 
 
 
TODO:

ollo:

übersicht: mehr infos, z.b. wer hat hochgeladen, Datum, 
playliste: editieren (löschen, editieren, Benutzer Rechte (editieren, public flag(read)))

download, upload

ID3 Tags bearbeiten, Infos hinzufügen (tag, votes)

Abspiel-History (recently played)



heiko:

coverflow, plugin Auswahl, rechte/lizenz

flashplayer für mp3s
 
 
 
 
 
 
 
 