package application.com.krise.utils;

import android.content.Context;
import android.content.SharedPreferences;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.message.BasicHeader;

import java.io.InputStream;
import java.net.HttpURLConnection;
import java.util.List;

/**
 * Created by dell on 16-Jul-16.
 */
public class PostWrapper {

    private static SharedPreferences prefs;

    /** Constants */
    public static String LOGOUT = "logout";
    public static String LOGIN = "register";
    public static String SIGNUP = "signup";

    public static void Initialize(Context context) {
        // helper = new ResponseCacheManager(context);
        prefs = context.getSharedPreferences("application_settings", 0);
    }

    public static String convertStreamToString(InputStream is) {
        try {
            return new java.util.Scanner(is).useDelimiter("\\A").next();
        } catch (java.util.NoSuchElementException e) {
            return "";
        }
    }

    public static Object[] postRequest(String Url, List<NameValuePair> nameValuePairs, String type,
                                       Context appContext) {

        Object[] resp = new Object[] { "failed", appContext.getResources().getString(R.string.could_not_connect),
                new User() };

        try {

            HttpResponse response = getPostResponse(Url, nameValuePairs, appContext);
            int responseCode = response.getStatusLine().getStatusCode();

            if (responseCode == HttpURLConnection.HTTP_OK) {
                InputStream is = CommonLib.getStream(response);
                if (type.equals(LOGIN)) {
                    resp = ParserJson.parseGLoginResponse(is);
                } else if (type.equals(SIGNUP)) {
                    resp = ParserJson.parseGLoginResponse(is);
                }
            }
            // else {
            // logErrorResponse(url, response);
            // }

        } catch (Exception E) {
            E.printStackTrace();
            return resp;
        }
        return resp;
    }

    /**public static HttpResponse getPostResponse(String Url, List<NameValuePair> nameValuePairs, Context appContext)
            throws Exception {

        HttpPost httpPost = new HttpPost(Url + CommonLib.getVersionString(appContext));
        httpPost.addHeader(new BasicHeader("client_id", CommonLib.CLIENT_ID));
        httpPost.addHeader(new BasicHeader("app_type", CommonLib.APP_TYPE));

        if (nameValuePairs != null)
            httpPost.setEntity(new UrlEncodedFormEntity(nameValuePairs, "UTF-8"));

        return HttpManager.execute(httpPost);
    }**/

}
