# Device API Documentation

## Overview

This document describes the two device APIs created for the ADLAUNCHER application.

## API Endpoints

### 1. Device Authentication API

**Endpoint:** `POST /api/device/get_auth`

**Description:** Authenticates a device using its unique_id

**Parameters:**

-   `device_id` (string, required): The unique_id of the device

**Response:**

```json
{
    "status": "success",
    "message": "Device authenticated successfully",
    "device_id": 1,
    "device_name": "Device Name"
}
```

**Error Responses:**

```json
{
    "status": "error",
    "message": "device_id parameter is required"
}
```

```json
{
    "status": "error",
    "message": "Device not found"
}
```

### 2. Get New Data API

**Endpoint:** `POST /api/device/get_new_data`

**Description:** Retrieves schedule data for an authenticated and active device

**Parameters:**

-   `device_id` (string, required): The unique_id of the device

**Response:**

```json
{
    "status": "success",
    "message": "Data retrieved successfully",
    "data": [
        {
            "device_unique_id": "DEVICE_001",
            "layout_type": "full_screen",
            "screen_no": 1,
            "screen_height": 1080,
            "screen_width": 1920,
            "schedule_start_date_time": "2024-01-01 10:00:00",
            "schedule_end_date_time": "2024-01-01 18:00:00",
            "play_forever": false,
            "medias": [
                {
                    "media_type": "image",
                    "title": "Sample Image",
                    "media_file": "sample.jpg",
                    "media_url": "http://your-domain.com/storage/media/sample.jpg"
                }
            ]
        }
    ]
}
```

**Error Responses:**

```json
{
    "status": "error",
    "message": "Device is not active"
}
```

```json
{
    "status": "error",
    "message": "No active layout found for device"
}
```

## Layout Types

-   `full_screen`: Full screen layout
-   `split_screen`: Split screen layout (2 screens)
-   `three_grid_screen`: Three grid screen layout (3 screens)
-   `four_grid_screen`: Four grid screen layout (4 screens)

## Usage Examples

### cURL Examples

**Device Authentication:**

```bash
curl -X POST http://your-domain.com/api/device/get_auth \
  -H "Content-Type: application/json" \
  -d '{"device_id": "DEVICE_001"}'
```

**Get New Data:**

```bash
curl -X POST http://your-domain.com/api/device/get_new_data \
  -H "Content-Type: application/json" \
  -d '{"device_id": "DEVICE_001"}'
```

### JavaScript Examples

**Device Authentication:**

```javascript
fetch("/api/device/get_auth", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        device_id: "DEVICE_001",
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

**Get New Data:**

```javascript
fetch("/api/device/get_new_data", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        device_id: "DEVICE_001",
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

## Performance & Scalability Features

### Caching

-   **Device Authentication**: 5-minute cache for device lookup
-   **Device Data**: 1-minute cache for schedule and media data
-   **Rate Limiting**: 100 requests per minute per device

### Optimizations

-   **Single Query**: Uses optimized JOIN queries instead of multiple database calls
-   **Concurrent Requests**: Handles multiple simultaneous API calls efficiently
-   **Memory Efficient**: Minimal memory usage with selective field queries

### API Status Endpoint

**Endpoint:** `GET /api/device/status`

**Description:** Get API status and performance metrics

**Response:**

```json
{
    "status": "success",
    "api_version": "1.0",
    "timestamp": "2024-01-01T10:00:00.000Z",
    "cache_stats": {
        "cache_driver": "file",
        "cache_prefix": "laravel_cache"
    },
    "database_stats": {
        "total_devices": 50,
        "active_devices": 45,
        "total_schedules": 200
    },
    "endpoints": {
        "get_auth": "/api/device/get_auth",
        "get_new_data": "/api/device/get_new_data",
        "api_status": "/api/device/status"
    }
}
```

## Notes

-   Both APIs require POST requests
-   The `device_id` parameter should match the `unique_id` field in the Device model
-   The device must have status = 1 (active) for the get_new_data API to work
-   Media files are served from the `storage/media/` directory
-   The APIs return JSON responses with appropriate HTTP status codes
-   **Rate Limiting**: Maximum 100 requests per minute per device
-   **Caching**: Responses are cached for better performance with multiple concurrent requests
-   **Scalability**: Optimized for handling multiple devices calling APIs simultaneously
