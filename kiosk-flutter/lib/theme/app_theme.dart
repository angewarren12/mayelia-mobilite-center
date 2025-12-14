import 'package:flutter/material.dart';

class AppTheme {
  // Couleurs Mayelia (basées sur le design web)
  static const Color mayelia50 = Color(0xFFF2FAF5);
  static const Color mayelia100 = Color(0xFFE6F4EC);
  static const Color mayelia200 = Color(0xFFC0E4CF);
  static const Color mayelia300 = Color(0xFF9AD3B2);
  static const Color mayelia400 = Color(0xFF4EB279);
  static const Color mayelia500 = Color(0xFF02913F); // Couleur principale
  static const Color mayelia600 = Color(0xFF028339);
  static const Color mayelia700 = Color(0xFF01662C);
  static const Color mayelia800 = Color(0xFF014920);
  static const Color mayelia900 = Color(0xFF012C13);

  // Autres couleurs
  static const Color purple500 = Color(0xFF9333EA);
  static const Color purple100 = Color(0xFFF3E8FF);
  static const Color purple600 = Color(0xFF7C3AED);

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      colorScheme: ColorScheme.fromSeed(
        seedColor: mayelia500,
        primary: mayelia500,
        secondary: purple500,
        surface: Colors.white,
      ),
      scaffoldBackgroundColor: const Color(0xFFF9FAFB),
      cardTheme: CardThemeData(
        elevation: 8,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(24),
        ),
        color: Colors.white,
      ),
      buttonTheme: ButtonThemeData(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          elevation: 0,
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
      ),
      textTheme: const TextTheme(
        displayLarge: TextStyle(
          fontSize: 48,
          fontWeight: FontWeight.bold,
          color: Color(0xFF1F2937), // gray-800
        ),
        displayMedium: TextStyle(
          fontSize: 36,
          fontWeight: FontWeight.bold,
          color: Color(0xFF1F2937),
        ),
        displaySmall: TextStyle(
          fontSize: 24,
          fontWeight: FontWeight.bold,
          color: Color(0xFF1F2937),
        ),
        headlineMedium: TextStyle(
          fontSize: 32,
          fontWeight: FontWeight.bold,
          color: Color(0xFF1F2937),
        ),
        titleLarge: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.bold,
          color: Color(0xFF1F2937),
        ),
        bodyLarge: TextStyle(
          fontSize: 18,
          color: Color(0xFF6B7280), // gray-500
        ),
        bodyMedium: TextStyle(
          fontSize: 16,
          color: Color(0xFF6B7280),
        ),
      ),
    );
  }

  // Box shadows (équivalents Tailwind)
  static List<BoxShadow> shadowXl = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.1),
      blurRadius: 25,
      offset: const Offset(0, 10),
    ),
  ];

  static List<BoxShadow> shadow2xl = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.15),
      blurRadius: 35,
      offset: const Offset(0, 15),
    ),
  ];
}

