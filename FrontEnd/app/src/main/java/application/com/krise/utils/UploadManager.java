package application.com.krise.utils;

import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Debug;
import android.widget.Toast;

import java.io.InputStream;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.List;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicHeader;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.protocol.HTTP;
import org.apache.http.util.EntityUtils;
import org.json.JSONObject;

import application.com.krise.KriseApplication;

/**
 * Created by dell on 16-Jul-16.
 */
public class UploadManager {
    public static Hashtable<Integer, AsyncTask> asyncs = new Hashtable<Integer, AsyncTask>();
    public static Context context;
    private static SharedPreferences prefs;
    private static ArrayList<UploadManagerCallback> callbacks = new ArrayList<UploadManagerCallback>();
    private static KriseApplication zapp;

    public static void setContext(Context context) {
        UploadManager.context = context;
        prefs = context.getSharedPreferences("application_settings", 0);

        if (context instanceof KriseApplication) {
            zapp = (KriseApplication) context;
        }
    }

    public static void addCallback(UploadManagerCallback callback) {
        if (!callbacks.contains(callback)) {
            callbacks.add(callback);
        }

    }

    public static void removeCallback(UploadManagerCallback callback) {
        if (callbacks.contains(callback)) {
            callbacks.remove(callback);
        }
    }


    public static void login(String username, String email, String password) {
        for (UploadManagerCallback callback : callbacks) {
            callback.uploadStarted(CommonLib.LOGIN, 0, email, null);
        }
        new Login().executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR,
                new Object[]{username, email, password});

    }

    private static class Login extends AsyncTask<Object, Void, Object[]> {

        private String name, email, password;

        @Override
        protected Object[] doInBackground(Object... params) {

            Object result[] = null;
            name = (String) params[0];
            email = (String) params[1];
            password = (String) params[2];

            HttpClient client = new DefaultHttpClient();
            HttpResponse response;
            JSONObject json = new JSONObject();
            String URL= CommonLib.SERVER_URL+"api/login";

            try {
                HttpPost post = new HttpPost(URL);
                json.put("email", email);
                json.put("password", password);
                StringEntity se = new StringEntity( json.toString());
                se.setContentType(new BasicHeader(HTTP.CONTENT_TYPE, "application/json"));
                post.setEntity(se);
                response = client.execute(post);
                HttpEntity entity = response.getEntity();
                String output = EntityUtils.toString(entity);
                JSONObject jObject = new JSONObject(output);
                String mResponse = jObject.getString("response");
                if(mResponse.equalsIgnoreCase("success")){
                    String token = jObject.getString("token");
                    result = new Object[2];
                    result[0] = mResponse;
                    result[1] = token;
                }else{
                    result = new Object[2];
                    result[0] = mResponse;
                    result[1] = "error occured";
                }


            } catch(Exception e) {
                e.printStackTrace();
            }


            return result;
        }

        @Override
        protected void onPostExecute(Object[] arg) {
            if (arg[0].equals("failure"))
                Toast.makeText(context, (String) arg[1], Toast.LENGTH_SHORT).show();

            for (UploadManagerCallback callback : callbacks) {
                callback.uploadFinished(CommonLib.LOGIN, 0, 0, arg[1], 0,
                        arg[0].equals("success"), "");
            }
        }
    }


}
