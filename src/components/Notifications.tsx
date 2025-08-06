import React from 'react';

interface NotificationProps {
  message: string;
  type: 'success' | 'error' | 'info';
}

const Notifications: React.FC<NotificationProps> = ({ message, type }) => {
  const notificationStyle: React.CSSProperties = {
    padding: '10px',
    margin: '10px 0',
    borderRadius: '5px',
    color: 'white',
    backgroundColor: type === 'error' ? 'red' : type === 'success' ? 'green' : 'blue',
  };

  return (
    <div style={notificationStyle}>
      {message}
    </div>
  );
};

export default Notifications;


