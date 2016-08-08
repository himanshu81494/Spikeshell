package application.com.krise.views;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Parcelable;
import android.support.v4.view.PagerAdapter;
import android.support.v4.view.ViewPager;
import android.support.v4.view.ViewPager.OnPageChangeListener;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.Animation;
import android.view.animation.Animation.AnimationListener;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import java.util.Timer;
import java.util.TimerTask;
import java.util.concurrent.atomic.AtomicInteger;

import application.com.krise.R;
import application.com.krise.utils.CommonLib;

public class SplashScreen extends Activity {

    private int width;
    private int height;
    private Activity mContext;
    private boolean destroyed = false;
    private ViewPager mViewPager;
    private RelativeLayout mSignupContainer;
    ProgressDialog zProgressDialog;
    private TextView mSignupText;


    private ProgressDialog z_ProgressDialog;
    private boolean dismissDialog = false;

    /** Constant, randomly selected */
    public final int RESULT_FACEBOOK_LOGIN_OK = 1432; // Random numbers
    public final int RESULT_GOOGLE_LOGIN_OK = 1434;
    private String error_responseCode = "";
    private String error_exception = "";
    private String error_stackTrace = "";
    private boolean windowHasFocus = false;
    private final static int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    //    GoogleCloudMessaging gcm;
    AtomicInteger msgId = new AtomicInteger();
    String regId;
    int hardwareRegistered = 0;
    private SharedPreferences prefs;
    //    private ZApplication zapp;
    private String APPLICATION_ID;

    //    private GoogleApiClient mGoogleApiClient;
    private static final int DIALOG_GET_GOOGLE_PLAY_SERVICES = 1;

    public static final int RC_SIGN_IN = 0;
    public String TAG = "Google Plus Login";
    /* Should we automatically resolve ConnectionResults when possible? */
    protected boolean mShouldResolve = false;
    private boolean mIsResolving = false;
    boolean isActivityRunning;

    private boolean hasSwipedPager = false;

    private boolean insideApp = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_splash_screen);

        // initialize variables
        mContext = this;
        width = getWindowManager().getDefaultDisplay().getWidth();
        height = getWindowManager().getDefaultDisplay().getHeight();

        prefs = getSharedPreferences("application_settings", 0);
        APPLICATION_ID = prefs.getString("app_id", "");

        mShouldResolve = false;

        if(getIntent() != null && getIntent().getExtras() != null)
            insideApp = getIntent().getBooleanExtra("insideApp", false);

        // initialize views
        mViewPager = (ViewPager) findViewById(R.id.tour_view_pager);
        mSignupContainer = (RelativeLayout) findViewById(R.id.signup_container);
        mSignupText= (TextView)findViewById(R.id.signup_text);


        TourPagerAdapter mTourPagerAdpater = new TourPagerAdapter();
        ((ViewPager) mViewPager).setAdapter(mTourPagerAdpater);

        ((ViewPager) mViewPager).setOnPageChangeListener(new OnPageChangeListener() {
            int position = ((ViewPager) mViewPager).getOffscreenPageLimit();

            @Override
            public void onPageSelected(int arg0) {

                LinearLayout dotsContainer = (LinearLayout) findViewById(R.id.tour_dots);

                int index = 5;
                for (int count = 0; count < index; count++) {
                    ImageView dots = (ImageView) dotsContainer.getChildAt(count);

                    if (count == arg0)
                        dots.setImageResource(R.drawable.tour_image_dots_selected);
                    else
                        dots.setImageResource(R.drawable.tour_image_dots_unselected);
                }

                switch (arg0) {

                    case 0:
                        mSignupText.setText(getResources().getString(R.string.pre_signup_description));
                        mSignupText.setAllCaps(true);
                        findViewById(R.id.main_root).setBackgroundColor(Color.parseColor("#000000"));
                        break;
                    case 1:
                        mSignupText.setText(getResources().getString(R.string.tour_01));
                        findViewById(R.id.main_root).setBackgroundColor(Color.parseColor("#ff8b00"));
                        mSignupText.setAllCaps(false);

                        break;
                    case 2:
                        mSignupText.setText(getResources().getString(R.string.tour_02));
                        findViewById(R.id.main_root).setBackgroundColor(Color.parseColor("#ffaa00"));
                        mSignupText.setAllCaps(false);
                        break;
                    case 3:
                        mSignupText.setText(getResources().getString(R.string.tour_03));
                        findViewById(R.id.main_root).setBackgroundColor(Color.parseColor("#ffcf06"));
                        mSignupText.setAllCaps(false);
                        break;
                    case 4:
                        mSignupText.setText(getResources().getString(R.string.pre_signup_description));
                        findViewById(R.id.main_root).setBackgroundColor(Color.parseColor("#000000"));
                        mSignupText.setAllCaps(true);

                        break;
                }

            }

            @Override
            public void onPageScrolled(int arg0, float arg1, int arg2) {
            }

            @Override
            public void onPageScrollStateChanged(int arg0) {
            }
        });


        ((ViewPager) mViewPager).setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                hasSwipedPager = true;
                return false;
            }
        });

        animate();

        updateDotsContainer();

        new Handler().postDelayed(new Runnable() {
            @Override
            public void run() {
                if (!destroyed)
                    startTimer();
            }
        }, 1000);
        fixSizes();

        findViewById(R.id.user_login).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent loginIntent = new Intent(SplashScreen.this, Login.class);
                loginIntent.putExtra("log", "login");
                startActivity(loginIntent);
                overridePendingTransition(R.anim.slide_in_up, R.anim.slide_out_bottom);
            }
        });

        findViewById(R.id.user_signup).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent signupIntent = new Intent(SplashScreen.this, Login.class);
                signupIntent.putExtra("log", "signup");
                startActivity(signupIntent);
                overridePendingTransition(R.anim.slide_in_up, R.anim.slide_out_bottom);
            }
        });
    }

    private void fixSizes() {
        mViewPager.getLayoutParams().height = 2 * height / 3 + width / 40;
//		mSignupContainer.getLayoutParams().height = height / 3 - width / 10 - width / 40 - width / 20;
    }


    @Override
    public void onResume() {
        super.onResume();
        isActivityRunning = true;
        if (dismissDialog) {
            if (z_ProgressDialog != null) {
                z_ProgressDialog.dismiss();
            }
        }
    }

    @Override
    protected void onPause() {
        isActivityRunning = false;
        super.onPause();
    }

    @Override
    public void onDestroy() {
        destroyed = true;
        if (zProgressDialog != null && zProgressDialog.isShowing()) {
            zProgressDialog.dismiss();
        }
        super.onDestroy();
    }

    private Animation animation1, animation2 ,animation3;

    private void animate() {

        try {
            final View tourDots = findViewById(R.id.tour_dots);

            animation2 = AnimationUtils.loadAnimation(getApplicationContext(), R.anim.slide_in_right);
            animation2.setDuration(500);
            animation2.restrictDuration(700);
            animation2.scaleCurrentDuration(1);

            animation3 = AnimationUtils.loadAnimation(getApplicationContext(), R.anim.slide_in_bottom);
            animation3.setDuration(400);
            animation3.restrictDuration(700);
            animation3.scaleCurrentDuration(1);
            animation3.setAnimationListener(new AnimationListener() {
                @Override
                public void onAnimationStart(Animation animation) {

                }

                @Override
                public void onAnimationEnd(Animation animation) {

                    tourDots.setVisibility(View.VISIBLE);
                    tourDots.startAnimation(animation2);
                    mSignupContainer.setVisibility(View.VISIBLE);

                }

                @Override
                public void onAnimationRepeat(Animation animation) {

                }
            });


            animation1 = AnimationUtils.loadAnimation(getApplicationContext(), R.anim.slide_up_center);
            animation1.setDuration(700);
            animation1.restrictDuration(700);
            animation1.setAnimationListener(new AnimationListener() {

                @Override
                public void onAnimationStart(Animation animation) {
                }

                @Override
                public void onAnimationRepeat(Animation animation) {
                }

                @Override
                public void onAnimationEnd(Animation animation) {
                    mSignupContainer.startAnimation(animation3);
                }
            });
            animation1.scaleCurrentDuration(1);
            mViewPager.startAnimation(animation1);


        } catch (Exception e) {
            mViewPager.setVisibility(View.VISIBLE);
            mSignupContainer.setVisibility(View.VISIBLE);
            findViewById(R.id.tour_dots).setVisibility(View.VISIBLE);
        }
    }

    /**
     * Registers the application with GCM servers asynchronously.
     * <p/>
     * Stores the registration ID and app versionCode in the application's
     * shared preferences.
     */



    /**
     * @return Application's version code from the {@code PackageManager}.
     */

    /**
     * Gets the current registration ID for application on GCM service.
     * <p/>
     * If result is empty, the app needs to register.
     *
     * @return registration ID, or empty string if there is no existing
     *         registration ID.
     */





    private class TourPagerAdapter extends PagerAdapter {

        @Override
        public int getCount() {
            return 5;
        }

        @Override
        public Object instantiateItem(ViewGroup collection, int position) {

            RelativeLayout layout = (RelativeLayout) getLayoutInflater().inflate(R.layout.tour_element, null);

            if (position == 0) {

                ImageView tour_logo = (ImageView) layout.findViewById(R.id.tour_logo);
                TextView tour_text= (TextView) layout.findViewById(R.id.tour_text);
                tour_text.setVisibility(View.GONE);
                tour_logo.getLayoutParams().width = 4 * width  / 8;
                tour_logo.getLayoutParams().height = 2 * width / 8;

                //setting image
                Bitmap bitmap = CommonLib.getBitmap(mContext, R.drawable.olaicon, width, height);
                tour_logo.setImageBitmap(bitmap);
                tour_logo.setVisibility(View.VISIBLE);

            } else if (position == 1) {

                ImageView tour_logo = (ImageView) layout.findViewById(R.id.tour_logo);
                tour_logo.getLayoutParams().width = width; //420
                tour_logo.getLayoutParams().height = ((220 * width) / 420);
//				// setting image
                Bitmap bitmap = CommonLib.getBitmap(mContext, R.drawable.olaicon, width, height);
                tour_logo.setImageBitmap(bitmap);
                tour_logo.setVisibility(View.VISIBLE);

            } else if (position == 2) {

                ImageView tour_logo = (ImageView) layout.findViewById(R.id.tour_logo);

                tour_logo.getLayoutParams().width = width;
                tour_logo.getLayoutParams().height = ((220 * width) / 420);
                // setting image
                Bitmap bitmap = CommonLib.getBitmap(mContext, R.drawable.olaicon, width, height);
                tour_logo.setImageBitmap(bitmap);
                tour_logo.setVisibility(View.VISIBLE);

            } else if (position == 3) {

                ImageView tour_logo = (ImageView)layout.findViewById(R.id.tour_logo);
                tour_logo.getLayoutParams().width = width;
                tour_logo.getLayoutParams().height = ((220 * width) / 420);

                Bitmap bitmap = CommonLib.getBitmap(mContext, R.drawable.olaicon, width, height);
                tour_logo.setImageBitmap(bitmap);
                tour_logo.setVisibility(View.VISIBLE);

            } else if (position == 4) {

                ImageView tour_logo = (ImageView) layout.findViewById(R.id.tour_logo);
                tour_logo.getLayoutParams().width = 4 * width  / 8;
                tour_logo.getLayoutParams().height = 2 * width / 8;

                Bitmap bitmap = CommonLib.getBitmap(mContext, R.drawable.olaicon, width, height);
                tour_logo.setImageBitmap(bitmap);
                tour_logo.setVisibility(View.VISIBLE);
            }
            collection.addView(layout, 0);
            return layout;
        }

        @Override
        public void destroyItem(ViewGroup collection, int position, Object view) {
            collection.removeView((View) view);
        }

        @Override
        public boolean isViewFromObject(View view, Object object) {
            return (view == object);
        }

        @Override
        public void finishUpdate(ViewGroup arg0) {
        }

        @Override
        public void restoreState(Parcelable arg0, ClassLoader arg1) {
        }

        @Override
        public Parcelable saveState() {
            return null;
        }

        @Override
        public void startUpdate(ViewGroup arg0) {
        }

    }

    private void updateDotsContainer() {

        LinearLayout dotsContainer = (LinearLayout) findViewById(R.id.tour_dots);
        dotsContainer.removeAllViews();

        int index = 5;

        for (int count = 0; count < index; count++) {
            ImageView dots = new ImageView(getApplicationContext());

            if (count == 0) {
                dots.setImageResource(R.drawable.tour_image_dots_selected);
                dots.setPadding(width / 40, 0, width / 40, 0);

            } else {
                dots.setImageResource(R.drawable.tour_image_dots_unselected);
                dots.setPadding(0, 0, width / 40, 0);
            }

            final int c = count;
            dots.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    try {
                        ((ViewPager) mViewPager).setCurrentItem(c);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });
            dotsContainer.addView(dots);
        }
    }


    int seconds = 12;
    Timer timer;
    private int mCurrentItem = 0;

    private void startTimer() {
        if (mContext == null || destroyed)
            return;

        timer = new Timer();

        timer.scheduleAtFixedRate(new TimerTask() {
            @Override
            public void run() {
                if (!destroyed) {

                    ((Activity) mContext).runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            if (!destroyed) {
                                seconds -= 1;
                                if (seconds <= 0) {
                                    seconds = 12;
                                    timer.cancel();
                                } else {
                                    mCurrentItem++;
                                    if (mCurrentItem < 5 && !hasSwipedPager)
                                        mViewPager.setCurrentItem(mCurrentItem);
                                }

                            }
                        }
                    });
                } else {
                    timer.cancel();
                }
            }
        }, 3000, 3000);
    }

}
