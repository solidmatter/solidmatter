package net.solidbytes.jukebox;

import net.solidbytes.tools.connection.sbConnection;
import android.app.Activity;
import android.os.Bundle;
import android.widget.TextView;

public class Activity_Charts extends Activity {
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        sbConnection.connect();
        
        TextView textview = new TextView(this);
        textview.setText("This is the Charts tab");
        setContentView(textview);
    }
}