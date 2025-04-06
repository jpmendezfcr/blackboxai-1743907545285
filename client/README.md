# Aplicación de Autenticación con React Native y Expo

Esta es una aplicación móvil de autenticación construida con React Native y Expo que implementa un sistema completo de autenticación con las siguientes características:

## Características

- Inicio de sesión de usuarios
- Registro de nuevos usuarios
- Recuperación de contraseña
- Manejo de estado de autenticación global
- Navegación protegida
- Diseño responsive y moderno
- Validación de formularios
- Manejo de errores
- Persistencia de sesión

## Tecnologías Utilizadas

- React Native
- Expo
- React Navigation
- Axios
- AsyncStorage
- Context API

## Estructura del Proyecto

```
src/
├── components/
│   ├── CustomButton.js
│   └── CustomInput.js
├── context/
│   └── AuthContext.js
├── navigation/
│   └── AppNavigator.js
├── screens/
│   ├── LoginScreen.js
│   ├── RegisterScreen.js
│   └── ForgotPasswordScreen.js
└── services/
    └── authService.js
```

## Instalación

1. Clona el repositorio
2. Instala las dependencias:
   ```bash
   npm install
   ```
3. Inicia la aplicación:
   ```bash
   npm start
   ```

## Uso

### Inicio de Sesión
- Ingresa tu email y contraseña
- Utiliza la opción "¿Olvidaste tu contraseña?" si necesitas recuperar tu cuenta
- Accede a la opción de registro si no tienes una cuenta

### Registro
- Completa el formulario con tu información
- El sistema validará tus datos
- Recibirás una confirmación al completar el registro

### Recuperación de Contraseña
- Ingresa tu email
- Recibirás instrucciones por correo electrónico
- Sigue los pasos para restablecer tu contraseña

## Componentes Reutilizables

### CustomButton
Botón personalizado con soporte para:
- Estados de carga
- Diferentes estilos (primario, secundario)
- Estados deshabilitados

### CustomInput
Campo de entrada personalizado con:
- Validación de errores
- Diferentes tipos de teclado
- Soporte para contraseñas
- Estados de foco

## Manejo de Estado

La aplicación utiliza Context API para manejar el estado global de autenticación, proporcionando:
- Estado de autenticación
- Funciones de inicio/cierre de sesión
- Persistencia de datos del usuario
- Estado de carga

## Servicios

### AuthService
Maneja todas las llamadas a la API relacionadas con la autenticación:
- Login
- Registro
- Recuperación de contraseña
- Manejo de tokens
- Persistencia de sesión

## Seguridad

- Validación de datos en el cliente
- Manejo seguro de contraseñas
- Tokens de autenticación
- Protección de rutas
- Manejo de sesiones

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE.md](LICENSE.md) para más detalles.