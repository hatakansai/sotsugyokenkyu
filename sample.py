\
#!/paty/to/python
# -*- conding:utf-8 -*-
import smbus
import time
import MySQLdb
import datetime

connector = MySQLdb.connect(host="localhost", db="sensor", user="**********", passwd="********", charset="utf8")
cursor = connector.cursor()

def writeDb():
        def temperature(a,n):
                bus = smbus.SMBus(1)
                data = bus.read_i2c_block_data(0x68,a,32)
                column = ""
                value = ""
                for i in range(16):
                        temp = int(data[2*i+1] << 8|data[2*i])
                        if i < 8:
                                column += "data_" + str(n) + "_" + str(i+1) + ","
                                value += str(temp * 0.25) + ","
                        elif i < 16:
                                column += "data_" + str(n+1) + "_" + str(i+1-8) + ","
                                value += str(temp * 0.25) + ","
                return column,value

        def thermistor():
                bus = smbus.SMBus(1)
                data = bus.read_i2c_block_data(0x68,0x0E,2)
                therm = int(data[1] << 8|data[0])
                return therm * 0.0625

        sql_32 = temperature(0x80,1)
        sql_64 = temperature(0xa0,3)
        sql_96 = temperature(0xc0,5)
        sql_128 = temperature(0xe0,7)

        gmtTime = datetime.datetime.now()
        jstTime = gmtTime; # + datetime.timedelta(hours=9)

        sql1 = "datetime,temp," + (sql_32[0] + sql_64[0] + sql_96[0] + sql_128[0]).rstrip(',')
        sql2 =  "'" + str(jstTime) + "'," + str(thermistor()) + "," + (sql_32[1] + sql_64[1] + sql_96[1] + sql_128[1]).rstrip(',')

        sql = "INSERT INTO grideye (" + sql1 + ") VALUES (" + sql2 + ")"

        cursor.execute(sql)
        connector.commit()

while 1:
        writeDb()
        time.sleep(1.0)
else: "loop done"

cursor.close()
connector.close()
