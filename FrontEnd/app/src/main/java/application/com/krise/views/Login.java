package application.com.krise.views;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

import application.com.krise.R;
import application.com.krise.utils.CommonLib;
import application.com.krise.utils.UploadManager;
import application.com.krise.utils.UploadManagerCallback;

public class Login extends Activity implements UploadManagerCallback {

    TextView button;
    EditText email,password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);

        String check = getIntent().getStringExtra("log");
        UploadManager.addCallback(this);

        if (check.equalsIgnoreCase("login"))
        {
            setContentView(R.layout.login_layout);
            ((TextView) findViewById(R.id.submit_button)).setText(getResources().getString(R.string.login));
            email = (EditText) findViewById(R.id.login_email);
            password = (EditText) findViewById(R.id.login_password);
            findViewById(R.id.submit_button).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String text1 = email.getText().toString();
                String text2 = password.getText().toString();


            }
        });
        }
        else if (check.equalsIgnoreCase("signup"))
        {
            setContentView(R.layout.signup_layout);
            ((TextView) findViewById(R.id.submit_button)).setText(getResources().getString(R.string.signup));
            ((TextView) findViewById(R.id.login_page_already_have_an_account)).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    setContentView(R.layout.login_layout);
                }
            });
        }
        else {
            setContentView(R.layout.login_layout);
        }
    }

    @Override
    public void uploadFinished(int requestType, int userId, int objectId, Object data, int uploadId, boolean status, String stringId) {
        if(requestType == CommonLib.LOGIN){
            if(status){
                String token = (String) data;
                Intent intent = new Intent();
            }
        }
    }

    @Override
    public void uploadStarted(int requestType, int objectId, String stringId, Object object) {

    }
}
