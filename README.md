# WCAG Stylesheet Switcher

Ein WordPress-Plugin, das es Benutzern ermöglicht, zwischen verschiedenen WCAG-konformen Stylesheets zu wechseln. Das Plugin bietet einen einfach zu bedienenden Button, der die Kontrast-Einstellungen der Website anpassen kann.

## Features

- Einfacher Kontrast-Switch-Button mit drei Positionierungsoptionen:
  - In der Navigation
  - Links (40% von oben)
  - Rechts (40% von oben)
- Anpassbare CSS-Regeln für den Kontrast-Modus
- CodeMirror-Integration für Syntax-Highlighting im Admin-Bereich
- Debug-Panel für Administratoren
- Persistente Einstellungen über LocalStorage
- Responsive Design
- Barrierefreie Implementierung

## Installation

1. Laden Sie das Plugin als ZIP-Datei herunter
2. Gehen Sie in Ihrem WordPress-Admin-Bereich zu "Plugins > Installieren"
3. Klicken Sie auf "Plugin hochladen" und wählen Sie die heruntergeladene ZIP-Datei aus
4. Aktivieren Sie das Plugin nach der Installation

## Verwendung

1. Gehen Sie zu "Einstellungen > WCAG Switcher" im WordPress-Admin-Bereich
2. Aktivieren Sie den Switcher mit der Checkbox
3. Wählen Sie die gewünschte Position des Buttons
4. Fügen Sie Ihre CSS-Regeln für den Kontrast-Modus hinzu
5. Speichern Sie die Einstellungen

## Anpassung

Sie können die CSS-Regeln für den Kontrast-Modus im Admin-Bereich anpassen. Die Regeln werden nur angewendet, wenn der Kontrast-Modus aktiv ist. Sie können auch die Body-Klasse `wcag-switcher-active` verwenden, um spezifischere Selektoren zu erstellen.

## Debug-Panel

Administratoren haben Zugriff auf ein Debug-Panel, das folgende Informationen anzeigt:
- Aktueller Kontrast-Status
- LocalStorage-Inhalt
- CSS-Datei-Inhalt

## Anforderungen

- WordPress 5.0 oder höher
- PHP 7.0 oder höher

## Lizenz

Dieses Plugin ist unter der GPL v2 oder später lizenziert.

## Autor

Andreas Huber - [Website](https://www.andreas-huber.at)

## Changelog

### 1.0.0
- Erste Version
- Grundlegende Funktionalität
- Drei Positionierungsoptionen
- Debug-Panel
- CodeMirror-Integration 