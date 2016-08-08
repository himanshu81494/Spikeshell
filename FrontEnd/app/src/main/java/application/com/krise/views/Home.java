package application.com.krise.views;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import application.com.krise.R;

public class Home extends AppCompatActivity {

    HomeFragment homeFragment;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.home_layout);

        homeFragment = HomeFragment.newInstance(getIntent().getExtras());
        getFragmentManager()
                .beginTransaction()
                .add(R.id.fragment_container, homeFragment, "home")
                .commit();
    }
}
