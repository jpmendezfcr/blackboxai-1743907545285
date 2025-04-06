import React from 'react';
import { TouchableOpacity, Text, StyleSheet, ActivityIndicator } from 'react-native';

const CustomButton = ({ 
  title, 
  onPress, 
  type = "primary", 
  loading = false,
  disabled = false 
}) => {
  return (
    <TouchableOpacity
      style={[
        styles.button,
        styles[`button_${type}`],
        disabled && styles.button_disabled
      ]}
      onPress={onPress}
      disabled={disabled || loading}
    >
      {loading ? (
        <ActivityIndicator color={type === "primary" ? "#fff" : "#000"} />
      ) : (
        <Text style={[
          styles.text,
          styles[`text_${type}`],
          disabled && styles.text_disabled
        ]}>
          {title}
        </Text>
      )}
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  button: {
    padding: 15,
    borderRadius: 10,
    alignItems: 'center',
    marginVertical: 5,
    width: '100%',
  },
  button_primary: {
    backgroundColor: '#2563eb',
  },
  button_secondary: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#2563eb',
  },
  button_disabled: {
    backgroundColor: '#ccc',
    borderWidth: 0,
  },
  text: {
    fontSize: 16,
    fontWeight: '600',
  },
  text_primary: {
    color: '#fff',
  },
  text_secondary: {
    color: '#2563eb',
  },
  text_disabled: {
    color: '#666',
  },
});

export default CustomButton;