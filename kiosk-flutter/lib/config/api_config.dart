class ApiConfig {
  static const String baseUrl = 'https://rendez-vous.mayeliamobilite.com';

  // Routes API
  static String checkRdv() => '$baseUrl/api/qms/check-rdv';
  static String storeTicket() => '$baseUrl/api/qms/tickets';
  static String getCentreInfo(int centreId) =>
      '$baseUrl/api/qms/centre/$centreId';
  static String getServices(int centreId) =>
      '$baseUrl/api/qms/services/$centreId';
  static String getQueueData(int centreId) =>
      '$baseUrl/api/qms/queue/$centreId';
  static String printTicket(int ticketId) =>
      '$baseUrl/qms/tickets/$ticketId/print';
}
