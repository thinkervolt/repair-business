import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import './styles.css';
import App from './App';
import { AuthProvider } from './auth/AuthContext';
import { I18nProvider } from './i18n/I18nContext';
import { ScanProvider } from './scan/ScanContext';

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <BrowserRouter>
            <AuthProvider>
                <I18nProvider>
                    <ScanProvider>
                        <App />
                    </ScanProvider>
                </I18nProvider>
            </AuthProvider>
        </BrowserRouter>
    </React.StrictMode>
);