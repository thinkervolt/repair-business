import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import RequireAuth from './auth/RequireAuth';
import AppLayout from './layouts/AppLayout';
import Login from './pages/Login';
import VerifyEmail from './pages/VerifyEmail';
import ForgotPassword from './pages/ForgotPassword';
import ResetPassword from './pages/ResetPassword';
import Dashboard from './pages/Dashboard';
import AdminOnlyRoute from './auth/AdminOnlyRoute';
import CustomersList from './pages/customers/CustomersList';
import CustomerForm from './pages/customers/CustomerForm';
import CustomerView from './pages/customers/CustomerView';
import RepairsList from './pages/repairs/RepairsList';
import RepairForm from './pages/repairs/RepairForm';
import RepairSettings from './pages/repairs/RepairSettings';
import RepairView from './pages/repairs/RepairView';
import ProductsList from './pages/inventory/ProductsList';
import ProductForm from './pages/inventory/ProductForm';
import ProductView from './pages/inventory/ProductView';
import TransactionsList from './pages/inventory/TransactionsList';
import CategoriesList from './pages/inventory/CategoriesList';
import InvoicesList from './pages/invoices/InvoicesList';
import InvoiceForm from './pages/invoices/InvoiceForm';
import InvoiceView from './pages/invoices/InvoiceView';
import InvoiceSettings from './pages/invoices/InvoiceSettings';
import PaymentsList from './pages/payments/PaymentsList';
import PaymentView from './pages/payments/PaymentView';
import ReportsPage from './pages/reports/ReportsPage';
import RegisterReport from './pages/reports/RegisterReport';
import SettingsPage from './pages/settings/SettingsPage';
import LogsList from './pages/logs/LogsList';
import TrashList from './pages/trash/TrashList';
import Profile from './pages/users/Profile';
import UsersList from './pages/users/UsersList';

export default function App() {
    return (
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/forgot-password" element={<ForgotPassword />} />
            <Route path="/reset-password" element={<ResetPassword />} />
            <Route path="/verify-email" element={<VerifyEmail />} />

            <Route
                path="/"
                element={
                    <RequireAuth>
                        <AppLayout />
                    </RequireAuth>
                }
            >
                <Route index element={<Navigate to="/dashboard" replace />} />
                <Route path="dashboard" element={<Dashboard />} />
                <Route path="customers" element={<CustomersList />} />
                <Route path="customers/create" element={<CustomerForm />} />
                <Route path="customers/:id" element={<CustomerView />} />
                <Route path="customers/:id/edit" element={<CustomerForm />} />
                <Route path="repairs" element={<RepairsList />} />
                <Route path="repairs/create" element={<RepairForm />} />
                <Route
                    path="repairs/settings"
                    element={
                        <AdminOnlyRoute>
                            <RepairSettings />
                        </AdminOnlyRoute>
                    }
                />
                <Route path="repairs/:id" element={<RepairView />} />
                <Route path="invoices" element={<InvoicesList />} />
                <Route path="invoices/create" element={<InvoiceForm />} />
                <Route path="invoices/:id" element={<InvoiceView />} />
                <Route
                    path="invoices/settings"
                    element={
                        <AdminOnlyRoute>
                            <InvoiceSettings />
                        </AdminOnlyRoute>
                    }
                />
                <Route path="inventory/products" element={<ProductsList />} />
                <Route path="inventory/products/create" element={<ProductForm />} />
                <Route path="inventory/products/:id" element={<ProductView />} />
                <Route path="inventory/products/:id/edit" element={<ProductForm />} />
                <Route path="inventory/transactions" element={<TransactionsList />} />
                <Route path="inventory/categories" element={<CategoriesList />} />
                <Route
                    path="payments"
                    element={
                        <AdminOnlyRoute>
                            <PaymentsList />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="payments/:id"
                    element={
                        <AdminOnlyRoute>
                            <PaymentView />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="reports"
                    element={
                        <AdminOnlyRoute>
                            <ReportsPage />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="reports/register"
                    element={
                        <AdminOnlyRoute>
                            <RegisterReport />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="settings"
                    element={
                        <AdminOnlyRoute>
                            <SettingsPage />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="users"
                    element={
                        <AdminOnlyRoute>
                            <UsersList />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="logs"
                    element={
                        <AdminOnlyRoute>
                            <LogsList />
                        </AdminOnlyRoute>
                    }
                />
                <Route
                    path="trash"
                    element={
                        <AdminOnlyRoute>
                            <TrashList />
                        </AdminOnlyRoute>
                    }
                />
                <Route path="profile" element={<Profile />} />
                <Route path="*" element={<Navigate to="/dashboard" replace />} />
            </Route>
        </Routes>
    );
}