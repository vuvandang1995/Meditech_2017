## Năm 2018
- Tốt nghiệp đại học.
- Theo vào một công ty để học tập và nghiên cứu Openstack, python
- Các mục tiêu năm 2018
	1. Tìm hiểu và cố gắng làm chủ Openstack
	2. Code Python ở level khá
	3. Làm chủ và phát triển giải pháp KVM-VDI








# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey has `on_delete` set to the desired behavior.
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from django.db import models
from django.contrib.auth.hashers import check_password
from user.models import *

class Users(models.Model):
    fullname = models.CharField(max_length=255)
    email = models.CharField(max_length=255)
    username = models.CharField(max_length=255)
    password = models.CharField(max_length=255)
    status = models.IntegerField(default=0)
    created = models.DateTimeField()

    class Meta:
        managed = True
        db_table = 'users'
    


class Topics(models.Model):
    name = models.CharField(max_length=255)
    status = models.IntegerField(default=0)

    class Meta:
        managed = True
        db_table = 'topics'


class Agents(models.Model):
    fullname = models.CharField(max_length=255)
    email = models.CharField(max_length=255)
    username = models.CharField(max_length=255)
    password = models.CharField(max_length=255)
    admin = models.IntegerField(default=0)
    leader = models.IntegerField(default=0)

    class Meta:
        managed = True
        db_table = 'agents'

class Tickets(models.Model):
    title = models.CharField(max_length=255)
    content = models.TextField()
    sender = models.ForeignKey('Users', models.SET(0), db_column='sender')
    topicid = models.ForeignKey('Topics', models.SET(0), db_column='topicid')
    status = models.IntegerField(default=0)
    datestart = models.DateTimeField()
    dateend = models.DateTimeField()
    attach = models.FileField(null=True, blank=True, upload_to='photos')
    class Meta:
        managed = True
        db_table = 'tickets'



class TicketAgent(models.Model):
    agentid = models.ForeignKey(Agents, models.CASCADE, db_column='agentid')
    ticketid = models.ForeignKey(Tickets, models.CASCADE, db_column='ticketid')

    class Meta:
        managed = True
        db_table = 'ticket_agent'


class ForwardTickets(models.Model):
    senderid = models.ForeignKey(Agents, models.CASCADE, db_column='senderid')
    receiverid = models.ForeignKey(Agents, models.CASCADE, db_column='receiverid')
    ticketid = models.ForeignKey('Tickets', models.CASCADE, db_column='ticketid')

    class Meta:
        managed = True
        db_table = 'forward_tickets'



def get_user(usname):
    try:
        return Users.objects.get(username=usname)
    except Users.DoesNotExist:
        return None


def get_agent(agentname):
    try:
        return Agents.objects.get(username=agentname)
    except Agents.DoesNotExist:
        return None


def get_user_email(email1):
    try:
        return Users.objects.get(email=email1)
    except Users.DoesNotExist:
        return None


def active(user):
        if user.status == 0:
            return False
        else:
            return True

def authenticate_user(username, password):
    u = get_user(username)
    if u is not None:
        login_valid = (u.username == username)
        #pwd_valid = check_password(password, u.password)
        pwd_valid = (password == u.password)
        status_valid = u.status
        if login_valid and pwd_valid and status_valid:
            return u
        else:
            return None
    else:
        return None


def authenticate_agent(agentname, agentpass):
    u = get_agent(agentname)
    if u is not None:
        login_valid = (u.username == agentname)
        #pwd_valid = check_password(password, u.password)
        pwd_valid = (agentpass == u.password)
        admin_valid = u.admin
        if login_valid and pwd_valid:
            if admin_valid:
                return 1
            else:
                return 0
        else:
            return None
    else:
        return None
