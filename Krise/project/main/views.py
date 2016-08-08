# project/main/views.py


#################
#### imports ####
#################
from project import db, bcrypt

from flask import redirect
from flask.ext.login import login_user, logout_user, \
  login_required, current_user
from flask import render_template, Blueprint, request, json
from flask.ext.login import login_required
from project.models import User
from werkzeug.security import generate_password_hash, \
     check_password_hash

################
#### config ####
################

main_blueprint = Blueprint('main', __name__,)


################
#### routes ####
################

@main_blueprint.route('/')
@login_required
def home():
    return render_template('main/index.html')


@main_blueprint.route('/showusers', methods=['GET', 'POST'])
@login_required
def showusers():
  if not current_user.admin:
    return redirect('/')
  users = User.query.all()
  return render_template("main/Users.html", users=users)


@main_blueprint.route('/api/filldetails', methods=['GET', 'POST'])
def filldetails():
  if request.headers['Content-Type'] == 'application/json':
    user = User.query.filter_by(user_token = request.json['token']).first()
    data = {}
    if user :
      user.mobileno = request.json['mobileno']
      user.latitude = request.json['lat']
      user.longitude = request.json['lon']
      user.contacts = request.json['contacts']
      user.gcmid  = request.json['gcmid']
      user.name = request.json['name']
        # if request.json['token'] == 
      db.session.commit()
      data['response'] = "success"  
      return json.dumps(data)
    else:
      data['response'] = "failure"  
      return json.dumps(data)


@main_blueprint.route('/api/getdetails', methods=['GET', 'POST'])
def getdetails():
  if request.headers['Content-Type'] == 'application/json':
    user = User.query.filter_by(user_token = request.json['token']).first()
    data = {}
    if user :
      data['email'] = user.email
      data['name'] = user.name
      data['mobileno'] = user.mobileno
      data['lat'] = user.latitude
      data['lon'] = user.longitude
      data['contacts'] = user.contacts
      data['gcmid'] = user.gcmid
      data['response'] = "success"
      return json.dumps(data)
    else:
      data['response'] = "failure"
      return json.dumps(data)

import urllib, json
@main_blueprint.route('/api/weather', methods=['GET', 'POST'])
def weather():
  api_key = "f3d7d2bc7eef02d1a59d6217dc182120"
  if request.headers['Content-Type'] == 'application/json':
    lat = request.json['lat']
    lon = request.json['lon']
    if lat and lon:
      url = "http://api.openweathermap.org/data/2.5/weather?lat="+ lat +"&lon="+ lon +"&appid=" + api_key
      print url
      response = urllib.urlopen(url)
      data = json.loads(response.read())
      resp = {}
      if data['cod'] == 200:
        resp['response'] = "success"
        resp['word'] = data['weather'][0]['main']
        resp['icon'] = data['weather'][0]['icon']
        resp['temp'] = data['main']['temp']
        resp['humi'] = data['main']['humidity']

        return json.dumps(resp)
      else:
        resp['response'] = "failure"
        return json.dumps(resp)



import httplib
@main_blueprint.route('/api/gcm', methods=['GET', 'POST'])
def gcm():
  data = {}
  if request.headers['Content-Type'] == 'application/json':
    user = User.query.filter_by(user_token = request.json['token']).first()
    if user :

      user.gcmregid = request.json['regId']
      user.gcmapikey = request.json['api_key']
      db.session.commit()
      data['response'] = "success"
      return json.dumps(data)
    else:
      data['response'] = "failure"
      return json.dumps(data)


from gcm import *
@main_blueprint.route('/api/test', methods=['GET', 'POST'])
def test():
  # https://www.digitalocean.com/community/tutorials/how-to-create-a-server-to-send-push-notifications-with-gcm-to-android-devices-using-python
  data = {}
  gcm = GCM("AIzaSyCOV9NF0aE1yDc3SXp8_UwnnfmFpcRA3-c")
  message = {}
  message['Notification'] = "Push Notification"
  # message['type'] = "NOTIFICATION_DISASTER"
  message['title'] = "Emergency Notification"
  message['subtitle'] = "gcm demo"
  message['tickerText'] = "ticker"
  # message['vibrate'] = 2
  # message['sound'] = 3
    
  
  reg_id = "APA91bGcILcvKarkKKYfSUYGPYae0gh6lqypVCeLY0kwvqbn98jBilCYQ_HH3O69CCq63pkKfXMv8Gubo_xNvHQ7-BqrIipfuMwF6vkQFnUibeAFVgkbjhbz2313A-A-aoUZDfz3-VPU"
  response = gcm.plaintext_request(registration_id=reg_id, data = message)
  
  return json.dumps(message)



"""
  regtoken = "APA91bGcILcvKarkKKYfSUYGPYae0gh6lqypVCeLY0kwvqbn98jBilCYQ_HH3O69CCq63pkKfXMv8Gubo_xNvHQ7-BqrIipfuMwF6vkQFnUibeAFVgkbjhbz2313A-A-aoUZDfz3-VPU"
  apikey = "AIzaSyDrvGfB9D1t6WJPfOiZ1swauKo7NQ7-rm0"
  message = {}
  message['Notification'] = "Push Notification"
  message['type'] = "NOTIFICATION_DISASTER"
  message['title'] = "Emergency Notification"
  message['subtitle'] = "gcm demo"
  message['tickerText'] = "ticker"
  message['vibrate'] = 2
  message['sound'] = 3
    
  fields = {}
  fields['registration_ids'] = regtoken
  fields['data'] = message


  headers = {"Content-type": "application/json", "Authorization": "key="+apikey }
  Url = "android.googleapis.com/gcm/send"
  conn = httplib.HTTPConnection(Url)
  conn.request("POST", "", fields, headers)
  response = conn.getresponse()
  return response


"""