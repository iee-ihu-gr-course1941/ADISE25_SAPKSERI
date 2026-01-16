# The Dry Peaks API (Ξερή)

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)

**Συνοπτική Περίληψη του project**
Το "The Dry Peaks API" (ADISE25_SAPKSERI) είναι ένα project 

## Τεχνολογίες

* **Backend:** PHP 
* **Database:** MySQL / MariaDB
* **Data Exchange:** JSON

### 1. Ρίξιμο Φύλλου (Throw Card)

`POST /TheDryPeaks.php/throwcard`

**Παράμετροι:**
* `game_id` (varchar): .
* `card_number` (varchar): .

**Παράδειγμα Απάντησης:**
```json 
{
  "success": true,
  "game_status": "PLAYING",
  "action_type": "THROW_CARD",
  "move_result": {
    "card_played": { "suit": "H", "value": 10 },
    "captured": true,
    "points_gained": 10,
    "board_now": []
  }
}