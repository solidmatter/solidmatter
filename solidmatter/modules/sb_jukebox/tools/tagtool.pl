
use strict;
use MP3::Tag;
use MP3::Info;
use Getopt::Long;
 
 
################################################################################
# change here the direcory name to scan
################################################################################
my @sources = 
(
	"i:\\!_MP3s\\test",
	#"i:\\!_MP3s\\!_komplette_torrent",
	#"i:\\!_MP3s\\!_komplette CDs",
	
);

my $html_dir = "$sources[0]\\_html";

my $opt_folder;
my $opt_year;
my $opt_bitrate;
my $opt_rename;
my $opt_track_name_cont;
my $opt_cover;
my $opt_tag_exist;
my $opt_mp3_info;
my $opt_verbose;
my $opt_html;



################################################################################
#
#  MP3 ID3v2
################################################################################
#	AENC	Audio encryption
#	APIC	Attached picture
#	COMM	Comments
#	COMR	Commercial frame
#	ENCR	Encryption method registration
#	EQUA	Equalization
#	ETCO	Event timing codes
#	GEOB	General encapsulated object
#	GRID	Group identification registration
#	IPLS	Involved people list
#	LINK	Linked information
#	MCDI	Music CD identifier
#	MLLT	MPEG location lookup table
#	OWNE	Ownership frame
#	PRIV	Private frame
#	PCNT	Play counter
#	POPM	Popularimeter
#	POSS	Position synchronisation frame
#	RBUF	Recommended buffer size
#	RVAD	Relative volume adjustment
#	RVRB	Reverb
#	SYLT	Synchronized lyric/text
#	SYTC	Synchronized tempo codes
#	TALB	Album/Movie/Show title
#	TBPM	BPM (beats per minute)
#	TCOM	Composer
#	TCON	Content type
#	TCOP	Copyright message
#	TDAT	Date
#	TDLY	Playlist delay
#	TENC	Encoded by
#	TEXT	Lyricist/Text writer
#	TFLT	File type
#	TIME	Time
#	TIT1	Content group description
#	TIT2	Title/songname/content description
#	TIT3	Subtitle/Description refinement
#	TKEY	Initial key
#	TLAN	Language(s)
#	TLEN	Length
#	TMED	Media type
#	TOAL	Original album/movie/show title
#	TOFN	Original filename
#	TOLY	Original lyricist(s)/text writer(s)
#	TOPE	Original artist(s)/performer(s)
#	TORY	Original release year
#	TOWN	File owner/licensee
#	TPE1	Lead performer(s)/Soloist(s)
#	TPE2	Band/orchestra/accompaniment
#	TPE3	Conductor/performer refinement
#	TPE4	Interpreted, remixed, or otherwise modified by
#	TPOS	Part of a set
#	TPUB	Publisher
#	TRCK	Track number/Position in set
#	TRDA	Recording dates
#	TRSN	Internet radio station name
#	TRSO	Internet radio station owner
#	TSIZ	Size
#	TSRC	ISRC (international standard recording code)
#	TSSE	Software/Hardware and settings used for encoding
#	TYER	Year
#	TXXX	User defined text information frame
#	UFID	Unique file identifier
#	USER	Terms of use
#	USLT	Unsychronized lyric/text transcription
#	WCOM	Commercial information
#	WCOP	Copyright/Legal information
#	WOAF	Official audio file webpage
#	WOAR	Official artist/performer webpage
#	WOAS	Official audio source webpage
#	WORS	Official internet radio station homepage
#	WPAY	Payment
#	WPUB	Publishers official webpage
#	WXXX	User defined URL link frame
#
################################################################################

################################################################################
# DOCU for module MP3::Info
#
# http://search.cpan.org/dist/MP3-Info/lib/MP3/Info.pm
#
#  Hash-Keys:
#    VERSION         MPEG audio version (1, 2, 2.5)
#    LAYER           MPEG layer description (1, 2, 3)
#    STEREO          boolean for audio is in stereo
#    VBR             boolean for variable bitrate
#    BITRATE         bitrate in kbps (average for VBR files)
#    FREQUENCY       frequency in kHz
#    SIZE            bytes in audio stream
#    OFFSET          bytes offset that stream begins
#    SECS            total seconds
#    MM              minutes
#    SS              leftover seconds
#    MS              leftover milliseconds
#    TIME            time in MM:SS
#    COPYRIGHT       boolean for audio is copyrighted
#    PADDING         boolean for MP3 frames are padded
#    MODE            channel mode (0 = stereo, 1 = joint stereo,
#                    2 = dual channel, 3 = single channel)
#    FRAMES          approximate number of frames
#    FRAME_LENGTH    approximate length of a frame
#    VBR_SCALE       VBR scale from VBR header
################################################################################

################################################################################
# DOCU for module MP3::Tag
#
# http://search.cpan.org/~ilyaz/MP3-Tag-0.92/Tag/ID3v2.pm
################################################################################



sub print_info
{
	my ($dir) = @_;   
	print "===============================================================================\n";
	print " scan MP3 files for TAG INFOs";
	print " search folder: $dir\n";
	print "===============================================================================\n";

}


sub print_usage()
{
	print "$0 [option] \n";
	print "\n";
	print "  options:\n";
	print "     -f [DIR]  sources directory to scan\n";
	print "     -e        check if ID3 tag exist\n";
	print "     -y        check year info in ID3 tag \n";
	print "               and check if year info is in directory name\n";
	print "     -b        check bitrate is not 128kB/s or less\n";
	print "               and that bitrate is not 320kB/s\n";
	print "                 [with -r the directory name can be changed]\n";
	print "                 [e.g.: ARTIST - ALBUM => ARTIST - ALBUM {128kbs}]\n";
	print "     -c        check if cover is present and only one\n";
	print "                 [with -r the additional covers will be removed]\n";
	print "\n";           
	print "     -r        this option changes path or filename or removes cover\n";
	print "     -i        show MP3 ID3 tag info\n";	
	print "\n";           
	print "     -g        generate html index page\n";
	print "\n";           
	print "     -n        check that all track numbers are continuously   [tbd]\n";
	print "\n";           
	print "     -v        verbose mode\n";
	print "\n";           
	print "     -h        print this help\n";
	print "\n";
}

sub read_directory
{
	my ($dir) = @_;
	opendir MYDIR, $dir;
	my @contents = readdir MYDIR;
	closedir MYDIR;
	
	return @contents;
}

sub get_dir_name_from_path
{
	my ($dir) = @_;
	my $name=$dir;
	
	if($name =~ /.*\\(.*)/ ){
		$name = $1;
	}
	return $name;
}

sub check_mp3_bitrate
{
	my ($dir) = @_;
	my $found_128 = 0;
	my $found_320 = 0;
	my @files = read_directory( $dir );
	
	foreach my $file  (@files){
		if( $file eq "." || $file eq ".."){
			# skip . and ..
			next;
		}
		
		if( -e "$dir\\$file" && $file =~ /.*mp3/){
			my $mp3info = get_mp3info("$dir\\$file");
			my $bitrate = $mp3info->{BITRATE};
			my $vbr     = $mp3info->{VBR};
		   
			if($bitrate <= 128 && !$vbr){
				$found_128 = 1;
			}elsif($bitrate >= 320 && !$vbr){
				$found_320 = 1;
			}
		}
	}
	
	my $dir_name = get_dir_name_from_path( $dir );
	my $rename_str;
	
	if( $found_128 ){
		print("Album:  $dir_name\n");
		print ("  ## album has 128kBs\n");
		$rename_str = " - {128kbs}";
			
	}
	if( $found_320 ){
		print("Album:  $dir_name\n");
		print ("  ## album has 320kBs\n");
		$rename_str = " - {320kbs}";
	}
	if($opt_rename){
		if( !($dir =~ /.*$rename_str/) ) {
			print ("  ## Rename dir\n");
			rename("$dir", "$dir$rename_str");
		}
	}
}

sub get_year_from_path
{
	my ($dir) = @_;
	my $year;
	
	if( $dir =~ /.* - \[([0-9]{4})\] - .*/){
		$year = $1;
		
		#print "### $year from Directory Name\n";
	}
	
	return $year;
}

sub check_mp3_year
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	my $year_path = get_year_from_path($dir);
	
	foreach my $file  (@files){
		if( $file eq "." || $file eq ".."){  # skip . and ..
			next;
		}
		if( -e "$dir\\$file" && $file =~ /.*mp3/){
			my $id3v1_year;
			my $id3v2_year;
			
			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$file");
			$id3->get_tags();
			
			if( !defined($year_path) ) {
				if(defined($dir_name)){
					print ("Album:  $dir_name\n");
					undef($dir_name);
				}
				print "  #### FILE: $file\n";
				print "  ####   NO year info in path\n";
				return;
			}
			
			if(exists $id3->{ID3v1}){
				$id3v1_year = $id3->{ID3v1}->year;
				if($id3v1_year eq ""){
					undef $id3v1_year;
				}
			} 
			if(exists $id3->{ID3v2}){
				
				if($id3->{ID3v2}->frame_have("TYER")){
					my ($info, $name, @rest) = $id3->{ID3v2}->get_frame("TYER");
					$id3v2_year = $info;
					if($id3v2_year eq ""){
						undef $id3v2_year;
					}
				}			
			}
			
			if( (defined($year_path) && defined ($id3v1_year)) 
			    || 
			    (defined($year_path) && defined ($id3v2_year)) ){
			
				if(defined($dir_name)){
					print ("Album:  $dir_name\n");
					undef($dir_name);
				}
				
				
				if( defined($year_path) && defined ($id3v1_year)){
					if($year_path != $id3v1_year){	
						print "  #### FILE: $file\n";
						print "  ####   Path:   $year_path\n";
						print "  ####   ID3v1:  $id3v1_year\n";
					}
				}
				if( defined($year_path) && defined ($id3v2_year)){
					if($year_path != $id3v2_year){
						print "  #### FILE: $file\n";
						print "  ####   Path:   $year_path\n";
						print "  ####   ID3v2:  $id3v2_year\n";
					}
				}
			}elsif( !defined($id3v1_year) && !defined ($id3v2_year) ){
				if(defined($dir_name)){
					print ("Album:  $dir_name\n");
					undef($dir_name);
				}
				
				if( defined($year_path) && $opt_rename){
					#save year info in tag from path name
					if(exists $id3->{ID3v2}){
						
						if($id3->{ID3v2}->frame_have("TYER")){
							print "  +++ v2 year exist\n";
						}else{
							print "  +++ write year to v2 \n";
							$id3->{ID3v2}->year($year_path);
							$id3->{ID3v2}->write_tag();
						}
					}elsif(exists $id3->{ID3v1}){
					
					}
				}
				print "  #### FILE: $file\n";
				print "  ####   NO ID3 tag year info \n";
			}
		}
	}
}

sub check_tag_exist
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	my $year_path = get_year_from_path($dir);
		
	foreach my $entry  (@files){
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){

			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			
			$id3->get_tags();
			
			if( !exists $id3->{ID3v1} && !exists $id3->{ID3v2} ){
				print ("Album:  $dir_name\n");
				print "$entry\n";
				print "no ID3Tag present!!\n";
			}
		}
	}
}

sub check_mp3_cover
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	my $year_path = get_year_from_path($dir);
		
	foreach my $entry  (@files){
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){
			my $present = 0;
			my $count = 0;
			
			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			$id3->get_tags();
			
			if (exists $id3->{ID3v2}) {
				my $frames = $id3->{ID3v2}->get_frame_ids();
				
				foreach my $frame (keys %$frames) {
					# for each frame
					# get a key-value pair of content-description
  					my($value, $desc) = $id3->{ID3v2}->get_frame($frame);

					if($frame =~ /APIC.*/ || $frame =~ /PIC.*/){
						#print "FRAME: .. $frame .. -- DESC: $desc: \n";
						#print "  #### COVER found\n";
						
						$present = 1;
						$count++;
					}
				}
			}
			
			if(!$present){
				if(defined($dir_name)){
					print("Album:  $dir_name\n");
					undef($dir_name);
				}
				
				print "  #### NO COVER found  -";
				print "  $entry\n";
			}
			
			if($present && $count != 1){
				print ("$dir_name - $entry\n");
				print "  #### more than one cover found !!!!\n";
				
				if($opt_rename){
					my $frames = $id3->{ID3v2}->get_frame_ids();

					foreach my $frame (keys %$frames) {
						# for each frame
						# get a key-value pair of content-description
	  					my($value, $desc) = $id3->{ID3v2}->get_frame($frame);

						if($frame =~ /APIC0.*/){
							#print "FRAME: .. $frame .. -- DESC: $desc: \n";
							print "   #### remove COVER picture ID: $frame\n";
						
							$id3->{ID3v2}->remove_frame($frame);
							$id3->{ID3v2}->write_tag();
						}
					}
				}
			}
		}
	}
}

sub show_mp3_info
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	my $year_path = get_year_from_path($dir);
		
	foreach my $entry  (@files){
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){

			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			
			$id3->get_tags();
			
			if (exists $id3->{ID3v2}) {
			
				my $frames = $id3->{ID3v2}->get_frame_ids();	
				print "FILE: $entry\n";
				
				foreach my $frame (keys %$frames) {
					# for each frame
					# get a key-value pair of content-description
  					my($value, $desc) = $id3->{ID3v2}->get_frame($frame);
					print "  FRAME: .. $frame .. -- DESC: $desc: \n";
					
					if($frame =~ /TALB.*/){
						print "   #### ALBUM\n";
					}
					if($frame =~ /TIT2.*/){
						print "   #### SONG TITLE\n";
					}
					if($frame =~ /TRCK.*/){
						print "   #### TRACK NUM\n";
					}
					if($frame =~ /TYER.*/){
						print "   #### YEAR\n";
					}
					if($frame =~ /APIC.*/ || $frame =~ /PIC.*/){
						print "   #### APIC\n";
					}
				}
			}
		}
	}
}


sub tag_get_artist
{
	my ($id3) = @_;
	my $id3v2 = $id3->{ID3v2};
	my $artist = $id3v2->get_frame('TPE1');
		
	return $artist;
} 

sub tag_get_album
{
	my ($id3) = @_;
	my $id3v2 = $id3->{ID3v2};
	my $album = $id3v2->get_frame('TALB');
		
	return $album;
}

sub tag_get_song
{
	my ($id3) = @_;
	my $id3v2 = $id3->{ID3v2};
	my $song = $id3v2->get_frame('TIT2');
		
	return $song;
}

my $cover_index=100000;

sub cover_save_from_album
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
		
		
	$cover_index++;
	foreach my $entry  (@files	){
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){

			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			
			$id3->get_tags();
			
			if ( $id3->{ID3v2}) {																
				my $frames = $id3->{ID3v2}->get_frame_ids();				
				foreach my $frame (keys %$frames) {
  					my($value, $desc) = $id3->{ID3v2}->get_frame($frame);
					if($frame =~ /APIC.*/ || $frame =~ /PIC.*/){
						my $pic;
						if (ref $value){
						
							foreach my $i (keys %$value) {			
								if( $i eq "_Data"){
									if(!(-d "$html_dir\/cover")){
										mkdir("$html_dir\\cover");
									}
									open (HANDLE,">$html_dir\/cover\/$cover_index.jpg");
									binmode (HANDLE); 
									binmode ("$cover_index.jpg");
									print HANDLE $value->{$i};		
									close (HANDLE);
								}
							}
						}
						
						return $cover_index;
					}
				}
			}
		}
	}
	return -1;
}

sub mp3_get_length
{
	my ($mp3) = @_;
	
	my $mp3info = get_mp3info("$mp3");
	
	return $mp3info->{SECS};
}

sub mp3_get_length_min
{
	my ($mp3) = @_;
	
	my $mp3info = get_mp3info("$mp3");
	
	return $mp3info->{MM};
}

sub mp3_get_length_sec
{
	my ($mp3) = @_;
	
	my $mp3info = get_mp3info("$mp3");
	
	return $mp3info->{SS};
}

my %collection;
my $collection_cnt=0;

sub generate_m3u_playlist
{
	my ($dir, $filename) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	
	if(!(-d "$html_dir\/playlists")){
		mkdir("$html_dir\\playlists");
	}
	open (PLAYLIST,">$html_dir\/playlists\/$filename.m3u");
	print PLAYLIST "#EXTM3U\n";
	
	foreach my $entry  (@files	){
		
		my $artist = "";
		my $album  = "";
		my $song   = "";
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){
			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			$id3->get_tags();	
			if ( $id3->{ID3v2}) {				
				$artist = tag_get_artist($id3);
				#$album  = tag_get_album($id3);
				$song   = tag_get_song($id3);
			}
			my $length = mp3_get_length("$dir/$entry");
			$length = sprintf(  "%i", $length);

			print PLAYLIST "#EXTINF:$length,$artist - $song\n";
			print PLAYLIST "$dir\\$entry\n";
		}
	}
	close (PLAYLIST);
}


sub build_collection
{
	my ($dir) = @_;
	my @files = read_directory( $dir );
	my $dir_name  = get_dir_name_from_path( $dir );
	my $year_path = get_year_from_path($dir);
	my $num = 0;
	
	my $artist = "";
	my $album  = "";
	my $song   = "";
	
	my $data_index = cover_save_from_album($dir);
	
	print "Processing $dir\n";
	
	foreach my $entry  (@files	){
		if( $entry eq "." || $entry eq ".."){
			# skip . and ..
			next;
		}
		
		if( -e "$dir\\$entry" && $entry =~ /.*mp3$/){
		
			# create new MP3-Tag object
			my $id3 = MP3::Tag->new("$dir\\$entry");
			
			$id3->get_tags();
			
				
			if ( $id3->{ID3v2}) {				
				$artist = tag_get_artist($id3);
				$album  = tag_get_album($id3);
				$song   = tag_get_song($id3);
				$collection{$artist}->{$album}->{'year'} = $year_path;
				$collection{$artist}->{$album}->{'songs'}->[$num]->{'song'} = $song;
				
				
				
				my $len     = mp3_get_length("$dir/$entry");
				my $len_min = mp3_get_length_min("$dir/$entry");
				my $len_sec = mp3_get_length_sec("$dir/$entry");
				
				$collection{$artist}->{$album}->{'songs'}->[$num]->{'len'}     = $len;
				$collection{$artist}->{$album}->{'songs'}->[$num]->{'len_min'} = $len_min;
				$collection{$artist}->{$album}->{'songs'}->[$num]->{'len_sec'} = $len_sec;
				
				if($data_index != -1){
					$collection{$artist}->{$album}->{'songs'}->[$num]->{'index'} = $data_index;		
				}
				$num++;
			}
		}
	}
	
	$collection_cnt++;

	generate_m3u_playlist($dir, $data_index);
	
}

sub generate_html
{
	open (HANDLE,">$html_dir\\index.html");

# generate HTML header
print HANDLE<<"ENDE";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
       "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<title>My MP3 Database</title>
	
<style type="text/css">

	html {
		background-color: #333333;
		padding: 30px;
	}

	body {
		color: black; 

		font-size: 100.01%;
		font-family: Helvetica,Arial,sans-serif;
		margin: 0; 
		padding: 10px;
	}
 
	h1 {
		font-family: "Trebuchet MS", Verdana, Geneva, Arial, Helvetica, sans-serif;
		font-size: 1.5em;
		margin: 0 0 0.7em; padding: 0.3em;
		text-align: center;
		background-color: #eee;
	}
	h2 {
		font-family: "Trebuchet MS", Verdana, Geneva, Arial, Helvetica, sans-serif;
	}


 	div.Artist {
		font-size: 1.5em;
		text-align: left;
		background-color: #666;
		padding: 20px;
		/*border: 2px ridge silver;*/
	}
	div.Artist h2 {
		font-size: 1.2em;
		margin: 0 0 0.7em; 
		padding: 0.2em;
		text-align: left;
		background-color: #FC3;
	}
 
	div.Album { 
		font-size: 0.6em;
		margin: 0 75px 0 75px ; 
		padding: 0;
		border: 2px solid #FC3; 
		background-color: #222;
		color: #EEE;
		
	}
	div.Album img{ 
		border: 2px solid grey;
	}
	div.Album h3 {
		font-size: 1.2em;
		margin: 0 0 0.7em; 
		padding: 0.2em;
		text-align: left;
		
	}
	
	div.Album_list { 
		font-size: 0.6em;
		
		margin: 0; padding: 0;
		border: 2px solid #FC3; 
		background-color: #222;
		color: #EEE;
	}
	
	*.track_a {
		
		text-align: left;
		background-color: grey;
	}
	*.track_b {
		font-size: 1.2em;
		margin: 0 0 0.7em; 
		padding: 0.2em;
		text-align: left;
	}
	
	
	

	
</style>
</head>
<body>
<h1>MP3 - Database</h1>

ENDE

	# alphabetisch sortierte Ausgabe
	foreach my $artist ( sort(keys %collection) ) {

		if($artist eq ""){
			print "++++\n";
			next;
		}
		print HANDLE '<p>									';
		print HANDLE '<div class="Artist">					';
		print HANDLE '<h2>'.$artist.'</h2> 					';

		
		foreach my $album ( sort (keys %{$collection{$artist}} ) ) {
		
			my $index = $collection{$artist}->{$album}->{'songs'}->[0]->{'index'};
			my $track_count = @{$collection{$artist}->{$album}->{'songs'} };
			
			print HANDLE '<p>																				';
			print HANDLE '<div class="Album">																	';
			print HANDLE '<table border="0">																';
			print HANDLE '	<tr>																			';
			print HANDLE '		<td align="left"   valign="top">											';
			print HANDLE '			<a href="playlists/'.$index.'.m3u"> 									';
			print HANDLE '				<img src="cover/'.$index.'.jpg" width="200" height="200" alt="'.$album.'"> ';
			print HANDLE '			</a>  																	';
			print HANDLE '			<br>																		';
			print HANDLE '			'.$artist.'																		';
			print HANDLE '			<br>																		';
			print HANDLE '			['.$collection{$artist}->{$album}->{'year'}.']																		';
			print HANDLE '		</td>																		';
			print HANDLE '		<td>       </td>															';
			print HANDLE '		<td>																		';
			print HANDLE '			<table border="0" width="400">											';
			print HANDLE '			  <tr>																	';
			print HANDLE '				<td colspan="3"> <h3>'.$album.' </h3></td>							';
			print HANDLE '			  </tr>																	';
			for( my $i=0; $i < $track_count; $i++ ){
				my $min = $collection{$artist}->{$album}->{'songs'}->[$i]->{'len_min'};
				my $sec = sprintf("%02i",$collection{$artist}->{$album}->{'songs'}->[$i]->{'len_sec'});
				
				if($i%2 == 0){
					print HANDLE '			  <tr class="track_a">																';
					print HANDLE '				<td>'.($i+1).'</td>												';
					print HANDLE '				<td>'.$collection{$artist}->{$album}->{'songs'}->[$i]->{'song'}.'</td>		';			
					print HANDLE '				<td>'.$min.':'.$sec.'</td>										';
					print HANDLE '			  </tr>																';
				}else{
					print HANDLE '			  <tr>																';
					print HANDLE '				<td>'.($i+1).'</td>												';
					print HANDLE '				<td>'.$collection{$artist}->{$album}->{'songs'}->[$i]->{'song'}.'</td>		';			
					print HANDLE '				<td>'.$min.':'.$sec.'</td>										';
					print HANDLE '			  </tr>																';
				}
				
			}
			print HANDLE '			</table>																';
			print HANDLE '	</tr>																			';
			print HANDLE '</table>																			';
			print HANDLE '</div>																			';
			print HANDLE '</p>																				';
			print HANDLE '<br>																				';
		}
		print HANDLE '</div>								';
		print HANDLE '</p>	';
		#print HANDLE "<hr noshade size=3>"
		#<hr noshade width="300" size="3" align="left">
	}


# generate HTML footer
print HANDLE<<"ENDE";
</body></html>
ENDE


	close (HANDLE);
}


sub main
{
	if(@ARGV < 1) {
		print_usage();
		exit(1);
	}
	
	my $result = GetOptions (  'f=s'    => \$opt_folder,   #  check year
	                           'y'      => \$opt_year,     #  check year
	                           'b'      => \$opt_bitrate,  #  check bitrate
	                           'r'      => \$opt_rename,   #  option rename
	                           'n'      => \$opt_track_name_cont,
	                           'c'      => \$opt_cover,
	                           'e'      => \$opt_tag_exist,
	                           'i'      => \$opt_mp3_info,
	                           'g'      => \$opt_html,
	                           'v'      => \$opt_verbose,            );
	
	if( $opt_folder ){
		@sources='';
		$sources[0] = $opt_folder;
		
		if(!-d $sources[0]){
			print "!!!!WARNING!!!!\n";
			print "$sources[0] is not a valid directory!!\n";
			exit 1;
		}
		print "Override of sources with: $sources[0]\n";
	}
	
	
	foreach my $source  (@sources){
	
		my @dirctories = read_directory( $source );
		my $count = @dirctories;
		
		print_info($source);
	
		foreach my $album_dir  (@dirctories){
			if( $album_dir eq "." || $album_dir eq ".."){
				# skip . and ..
				next;
			}
					
			if( $opt_bitrate ){
				check_mp3_bitrate("$source\\$album_dir");
			}
			if( $opt_year ){
				check_mp3_year("$source\\$album_dir");
			}
			if( $opt_track_name_cont ){
				print "not implemented yet\n";
			}
			if( $opt_tag_exist ){
				check_tag_exist("$source\\$album_dir");
			}
			if( $opt_mp3_info ){
				show_mp3_info("$source\\$album_dir");
			}
			if( $opt_cover ){
				check_mp3_cover("$source\\$album_dir");
			}
			if( $opt_html ){
				build_collection("$source\\$album_dir");
			}
		}
	}
	
	if( $opt_html ){
		# now generate html code
		if(!(-d $html_dir)){
			mkdir($html_dir);
		}
		
		generate_html();
	}
}

main();

