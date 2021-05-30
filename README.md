# WP Voice Studio

WordPress enhancements for your voice studio.

## User Roles

When Voice Studio is:

- **Activated:** The following user roles are added.
- **Deactivated:** The following user roles are removed. Users with those roles are assigned their fallback role, and some custom user meta.
- **Uninstalled:** Custom user meta is removed.

| Role | Fallback Role |
| :--- | :--- |
| Student | Subscriber |
| Parent of Student | Subscriber |
| Teacher | Editor |
| Owner | Administrator |

## API

**REST namespace:** `voice-studio/v1`

| Route | HTTP Method | Description |
| :--- | :--- | :--- |
| `hello` | `GET` | Returns `hello` |
