#define N_ELEMS(x)  (sizeof(x) / sizeof((x)[0]))

// ================================================
// PINS
// ================================================
int rfTransmitter = 10;
int rfReceiver = 11;
int securityAlarm = 13;
int windowDoorSensor[] = {22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33};
int pirSensor[] = {34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45};
boolean pinActivated[53];
boolean modeStay[53];
boolean modeAway[53];
boolean modeNight[53];

// ================================================
// Data Variables
// ================================================
boolean enabled;
boolean lockLow[N_ELEMS(pirSensor)];
boolean takeLowTime[N_ELEMS(pirSensor)];
long unsigned int lowIn[N_ELEMS(pirSensor)];  // the time when the sensor outputs a low impulse
int alarmStatus;                              // 0 = Off, 1 = Warning, 2 = On
int alarmState = 3;                           // 0 = Stay, 1 = Away, 2 = Night, 3 = Disarm
boolean windowDoorStatus[N_ELEMS(windowDoorSensor)];
unsigned long previousMillis = 0;             // will store last time alarm status was set
unsigned long warningTimeStart = 0;           // will store last time alarm warning was set

// ================================================
// Configuration
// ================================================
long unsigned int alarmInterval = 3000;       // interval used for alarm. should be < 25
long unsigned int alarmWarnTimer = 60000;     // interval for alarm warning timer before going off the alarm. Default: 60 sec
long unsigned int sensorInterval = 5000;      // the amount of milliseconds the sensor has to be low before we assume all motion has stopped.

// ================================================
void setup () {
  // RF433
  pinMode(rfTransmitter, OUTPUT);
  pinMode(rfReceiver, INPUT);

  // Security Alarm
  pinMode(securityAlarm, OUTPUT);

  // Window/Door Sensors
  for ( int i = 0; i < N_ELEMS(windowDoorSensor); i++ )
  {
    pinMode(windowDoorSensor[i], INPUT);
    digitalWrite(windowDoorSensor[i], HIGH);
  }

  // Motion Sensors
  for ( int i = 0; i < N_ELEMS(pirSensor); i++ )
    pinMode(pirSensor[i], INPUT);

  Serial.begin(57600);
}

void loop() {

  // =====================
  // Input from Serial
  // =====================
  if (Serial.available()) {
    // ===========================
    // Select which mode
    // ---------------------------
    // 'P' => Activate Pin
    //    'dpin:' => digitalPin
    //      'T' => t
    //      'F' => f
    // 'M' => Mode
    //    '0' => stay
    //    '1' => away
    //    '2' => night
    //    '3' => disarm
    //    '4' => triggered
    // 's' => Security Alarm
    //    'w' => warning
    //    'a' => active
    //    'd' => disarm
    // 'r' => RF433 Transmitter
    // 'd' => Read digitalPin
    //    'dpin:' => digitalPin
    // 'S' => Stay
    //    'dpin:' => digitalPin
    //      'T' => T
    //      'F' => F
    // 'A' => Away
    //    'dpin:' => digitalPin
    //      'T' => T
    //      'F' => F
    // 'N' => Night
    //    'dpin:' => digitalPin
    //      'T' => T
    //      'F' => F
    // ===========================
    char menu;
    int pin, currentState;
    boolean tof;
    menu = Serial.read();
    delay(1);
    switch (menu)
    {
      case 'E': // Enable the Home Security
        if (Serial.read() == 'T')
        {
          enabled = true;
          Serial.println("HomeSecurity:1");
        }
        else
        {
          enabled = false;
          Serial.println("HomeSecurity:0");
        }
        break;
        
      case 'P': // Activate or deactivate a digitalPin
        pin = Serial.readStringUntil(':').toInt();
        if (Serial.read() == 'T')
        {
          pinActivated[pin] = true;
          Serial.println("ActivePin" + String(pin) + ":1");
        }
        else
        {
          pinActivated[pin] = false;
          Serial.println("ActivePin" + String(pin) + ":0");
        }
        break;

      case 'M':
        // Read next byte;
        currentState = alarmState;
        alarmState = Serial.read() - '0';

        if (alarmState >= 0 && alarmState <= 3)
        {
          Serial.print("Mode:");
          Serial.println(alarmState);
        }
        else
          alarmState = currentState;

        if (alarmStatus != 0)
        {
          alarmStatus = 0;
          printDigitalPinStatus(securityAlarm, alarmStatus);
        }
        break;
        
      case 's': // Security Alarm Example: 'aw' means turn on warning mode
        // Read next byte;
        menu = Serial.read();

        // Warning alarm
        if (menu == 'w')
        {
          alarmStatus = 1;
          warningTimeStart = millis();
        }
        // Active alarm
        else if (menu == 'a')
          alarmStatus = 2;
        // Disarm alarm
        else if (menu == 'd')
          alarmStatus = 0;
        printDigitalPinStatus(securityAlarm, alarmStatus);
        break;

      case 'r': // RF433. Example: 'r4712341239.' means to send data "4712341239" to RF433
        //rf433(Serial.readStringUntil(':'));
        break;

      case 'd': // Get current digital pin data and print
        pin = Serial.readStringUntil(':').toInt();
        printDigitalPinStatus(pin, digitalRead(pin));
        break;

      case 'S': // Set digitalPin for Stay
        pin = Serial.readStringUntil(':').toInt();
        if (Serial.read() == 'T')
        {
          modeStay[pin] = true;
          Serial.println("Stay" + String(pin) + ":1");
        }
        else
        {
          modeStay[pin] = false;
          Serial.println("Stay" + String(pin) + ":0");
        }
        break;

      case 'A': // Set digitalPin for Away
        pin = Serial.readStringUntil(':').toInt();
        if (Serial.read() == 'T')
        {
          modeAway[pin] = true;
          Serial.println("Away" + String(pin) + ":1");
        }
        else
        {
          modeAway[pin] = false;
          Serial.println("Away" + String(pin) + ":0");
        }
        break;

      case 'N': // Set digitalPin for Night
        pin = Serial.readStringUntil(':').toInt();
        if (Serial.read() == 'T')
        {
          modeNight[pin] = true;
          Serial.println("Night" + String(pin) + ":1");
        }
        else
        {
          modeNight[pin] = false;
          Serial.println("Night" + String(pin) + ":0");
        }
        break;
    }

  }

  if (enabled)
  {
    // Check for alarm status
    alarm();

    // =====================
    // Output to Serial
    // =====================
    // Window/Door Sensors
    for (int i = 0; i < N_ELEMS(windowDoorSensor); i++)
      if (pinActivated[windowDoorSensor[i]] == true)
        windowDoor(i);

    // Motion Sensors
    for (int i = 0; i < N_ELEMS(pirSensor); i++)
      if (pinActivated[pirSensor[i]] == true)
        motion(i);

    // RF433
  }
}

void alarm()
{
  // Warning
  if (alarmStatus == 1)
  {
    unsigned long currentMillis = millis();

    if (currentMillis - previousMillis >= alarmInterval) {
      // save the last time you blinked the LED
      previousMillis = currentMillis;

      digitalWrite(securityAlarm, HIGH);
      delay(25);
      digitalWrite(securityAlarm, LOW);
    }
    if (currentMillis - warningTimeStart >= alarmWarnTimer)
    {
      alarmStatus = 2;
      printDigitalPinStatus(securityAlarm, alarmStatus);
    }
  }
  // On
  else if (alarmStatus == 2)
    digitalWrite(securityAlarm, HIGH);
  // Off
  else
    digitalWrite(securityAlarm, LOW);
}

void motion(int i)
{
  int stat = digitalRead(pirSensor[i]);
  if (stat == 1) {
    if (!lockLow[i]) {
      //makes sure we wait for a transition to LOW before any further output is made:
      lockLow[i] = true;
      printDigitalPinStatus(pirSensor[i], stat);
      delay(50);
    }
    takeLowTime[i] = true;
  }

  else if (stat == 0) {
    if (takeLowTime[i]) {
      lowIn[i] = millis();          //save the time of the transition from high to LOW
      takeLowTime[i] = false;       //make sure this is only done at the start of a LOW phase
    }
    //if the sensor is low for more than the given sensorInterval,
    //we assume that no more motion is going to happen
    if (lockLow[i] && millis() - lowIn[i] > sensorInterval) {
      //makes sure this block of code is only executed again after
      //a new motion sequence has been detected
      lockLow[i] = false;
      printDigitalPinStatus(pirSensor[i], stat);
      delay(50);
    }
  }
}

void windowDoor(int i)
{
  int stat = digitalRead(windowDoorSensor[i]);
  if (stat > 0 && !windowDoorStatus[i])
  {
    printDigitalPinStatus(windowDoorSensor[i], stat);
    windowDoorStatus[i] = true;
    delay(50);
  }
  else if (stat == 0 && windowDoorStatus[i])
  {
    printDigitalPinStatus(windowDoorSensor[i], stat);
    windowDoorStatus[i] = false;
    delay(50);
  }
}

void printDigitalPinStatus(int pin, int stat)
{
  if (pin == securityAlarm)
  {
    if (stat == 2)
      Serial.println("Alarm:2");
    else if (stat == 1)
      Serial.println("Alarm:1");
    else
      Serial.println("Alarm:0");
  }
  else if (pin >= 22 && pin <= 33)
  {
    if (stat == 1)
      Serial.println("WindowDoor" + String(pin) + ":1");
    else
      Serial.println("WindowDoor" + String(pin) + ":0");
  }
  else if (pin >= 34 && pin <= 45)
  {
    if (stat == 1)
      Serial.println("Motion" + String(pin) + ":1");
    else
      Serial.println("Motion" + String(pin) + ":0");
  }
  else
    return;
  if (stat > 0)
    checkTriggersOfPin(pin);
}

void checkTriggersOfPin(int pin)
{
  // Stay || Away || Night
  if (((alarmState == 0 && modeStay[pin]) || (alarmState == 1 && modeAway[pin]) || (alarmState == 2 && modeNight[pin])) && alarmStatus == 0)
  {
    alarmStatus = 1;
    alarmState = 4;
    warningTimeStart = millis();
    Serial.println("Mode:4");
    Serial.println("Alarm:1");
  }

}

