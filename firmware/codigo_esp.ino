#include <WiFi.h>
#include <HTTPClient.h>
#include <Keypad.h>
#include <ESP32Servo.h>
#include <OneWire.h>
#include <DallasTemperature.h>
// O sensor de peso HX711 e suas bibliotecas foram removidos para simulação.

// ===== Configurações Wi-Fi =====
const char* ssid = "DevSistem";
const char* password = "12345678";
const char* serverName = "http://192.168.0.102/sua_pasta/salvar_dados.php";
// ===== Sensores e Atuadores =====
#define PINO_RELE 15
#define PINO_SERVO 2
#define PINO_BUZZER 23
#define PINO_TEMP 4
#define SENSOR_AGUA 35

Servo servo;
OneWire oneWire(PINO_TEMP);
DallasTemperature sensors(&oneWire);

// ===== Variáveis de estado =====
bool servoAberto = false;
bool bombaLigada = false;
float pesoSimulado = 3.0; // Variável para o peso simulado, inicia com 3.0 kg.

// ===== Teclado Matricial =====
const byte ROWS = 4;
const byte COLS = 4;
char keys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'}
};
byte colPins[COLS] = {25, 26, 27, 14};
byte rowPins[ROWS] = {32, 33, 13, 5};
Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

// ===== Usuários e Permissões =====
struct Usuario {
  String nome;
  String senha;
  bool pode_retirar;     // Permissao para operacao 'A'
  bool pode_manutencao;  // Permissao para operacoes 'B' e 'C'
};
Usuario usuarios[] = {
  // Nome, Senha, Retirar(A), Manutencao(B/C)
  {"Messi", "123", true, true},
  {"Maria", "456", false, true},
  {"Kant", "789", true, false} // Kant pode retirar (A), mas NÃO pode fazer manutenção (B/C)
};
int totalUsuarios = sizeof(usuarios)/sizeof(usuarios[0]);
// ===== Controle de Entrada =====
String currentInput = "";
char operacaoAtual = '\0';

// ===== Funções para o Buzzer =====
void tocarTom(int freq, int duracao) {
  long periodo = 1000000L / freq;
  long ciclos = (long)freq * duracao / 1000;

  for (long i = 0; i < ciclos; i++) {
    digitalWrite(PINO_BUZZER, HIGH);
    delayMicroseconds(periodo / 2);
    digitalWrite(PINO_BUZZER, LOW);
    delayMicroseconds(periodo / 2);
  }
}

void somSenhaCorreta() {
  tocarTom(1200, 100);
  delay(50);
  tocarTom(1500, 100);
  delay(50);
  tocarTom(1800, 150);
  delay(50);
  tocarTom(2000, 100);
}

void somSenhaIncorreta() {
  tocarTom(600, 150);
  delay(50);
  tocarTom(500, 150);
  delay(50);
  tocarTom(400, 200);
  delay(50);
  tocarTom(300, 150);
}

// ===== Função para enviar dados ao servidor =====
void enviarDados(String usuario, String operacao, bool sucesso, float temperatura, float peso, int agua, bool torneiraAberta, bool abastecendo) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverName) + 
                 "?peso=" + String(peso, 3) +
                 "&temperatura=" + String(temperatura) +
                 "&torneira=" + String(torneiraAberta ? 1 : 0) +
                 "&manutencao=" + String(abastecendo ? 1 : 0) + 
                 "&vazando=" + String(agua) + 
                 "&usuario=" + usuario +
                 "&operacao=" + operacao;
    http.begin(url);
    int httpCode = http.GET();
    
    if (httpCode > 0) {
      Serial.print("Dados enviados: ");
      Serial.println(url);
      Serial.print("Resposta do servidor: ");
      Serial.println(http.getString());
    } else {
      Serial.print("Erro HTTP: ");
      Serial.println(httpCode);
    }
    http.end();
  }
}

// ===== Função para obter o índice do usuário pelo login =====
int getUsuarioIndex(String senhaDigitada) {
  for (int i = 0; i < totalUsuarios; i++) {
    if (usuarios[i].senha == senhaDigitada) {
      return i; // Retorna o índice
    }
  }
  return -1; // Retorna -1 se a senha for inválida
}

// ===== SETUP =====
void setup() {
  Serial.begin(115200);

  pinMode(PINO_RELE, OUTPUT);
  pinMode(PINO_BUZZER, OUTPUT);
  pinMode(SENSOR_AGUA, INPUT);
  digitalWrite(PINO_RELE, LOW);

  servo.attach(PINO_SERVO);
  servo.write(0); // Servo fechado

  sensors.begin();
  
  WiFi.begin(ssid, password);
  Serial.print("Conectando ao Wi-Fi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConectado ao Wi-Fi!");
  Serial.println("Pronto! Pressione A, B ou C para selecionar uma operacao.");
}

// ===== LOOP =====
void loop() {
  char key = keypad.getKey();
  if (key) {
    if (operacaoAtual == '\0') {
      if (key == 'A' || key == 'B' || key == 'C') {
        operacaoAtual = key;
        currentInput = "";
        Serial.print("Operacao escolhida: ");
        Serial.println(operacaoAtual);
        Serial.println("Agora, digite a senha de 3 digitos.");
      }
    } else {
      if (isdigit(key)) {
        currentInput += key;
        Serial.print("*");
        
        if (currentInput.length() == 3) {
          Serial.println();
          
          int userIndex = getUsuarioIndex(currentInput);
          String usuario = (userIndex != -1) ? usuarios[userIndex].nome : "Desconhecido";

          sensors.requestTemperatures();
          float temperatura = sensors.getTempCByIndex(0);
          int agua = digitalRead(SENSOR_AGUA);

          // Variável de controle de autorização (Senha e Permissão)
          bool autorizado = (userIndex != -1);
          
          // Verifica a permissão se a senha estiver correta
          if (autorizado) {
              if (operacaoAtual == 'A' && !usuarios[userIndex].pode_retirar) {
                  autorizado = false;
              } else if ((operacaoAtual == 'B' || operacaoAtual == 'C') && !usuarios[userIndex].pode_manutencao) {
                  autorizado = false;
              }
          }

          if (autorizado) {
            // Senha correta E Permissão concedida
            Serial.println("Senha correta! Autorizado.");
            somSenhaCorreta(); 

            if (operacaoAtual == 'A') {
              // A - Ligar a bomba para retirada do líquido
              digitalWrite(PINO_RELE, HIGH);
              bombaLigada = true;
              pesoSimulado -= 0.1;
              Serial.println("Retirada autorizada - bomba ligada.");
              enviarDados(usuario, "Retirada", true, temperatura, pesoSimulado, agua, bombaLigada, servoAberto);
              delay(5000);
              digitalWrite(PINO_RELE, LOW);
              bombaLigada = false;
              Serial.println("Bomba desligada.");

            } else if (operacaoAtual == 'B') {
              // B - Abrir porta para manutenção
              servo.write(90);
              servoAberto = true;
              Serial.println("Manutencao autorizada - servo aberto.");
              enviarDados(usuario, "Manutencao", true, temperatura, pesoSimulado, agua, bombaLigada, servoAberto);
            } else if (operacaoAtual == 'C') {
              // C - Fechar porta de manutenção
              servo.write(0);
              servoAberto = false;
              pesoSimulado = 3.0;
              Serial.println("Finalizar manutencao - servo fechado.");
              enviarDados(usuario, "Finalizar", true, temperatura, pesoSimulado, agua, bombaLigada, servoAberto);
            }

          } else {
            // Senha incorreta OU Senha correta, mas Permissão negada
            somSenhaIncorreta();
            if (userIndex != -1) {
                 Serial.print("Acesso Negado: ");
                 Serial.print(usuario);
                 Serial.println(" nao tem permissao para esta operacao!");
                 // Acao Denied -> Sucesso = false, mas loga o usuario
                 enviarDados(usuario, String(operacaoAtual), false, temperatura, pesoSimulado, agua, bombaLigada, servoAberto);
            } else {
                 // Senha incorreta -> loga como Desconhecido
                 Serial.println("Senha incorreta!");
                 enviarDados("Desconhecido", String(operacaoAtual), false, temperatura, pesoSimulado, agua, bombaLigada, servoAberto);
            }
          }
          operacaoAtual = '\0';
          currentInput = "";
          Serial.println("Operacao concluida. Pressione A, B ou C para a proxima operacao.");
        }
      }
    }
  }
}