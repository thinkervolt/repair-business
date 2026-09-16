import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import RequireAuth from './auth/RequireAuth';
import AppLayout from './layouts/AppLayout';
import Login from './pages/Login';
import Register from './pages/Register';
import VerifyEmail from './pages/VerifyEmail';
import ForgotPassword from './pages/ForgotPassword';
import ResetPassword from './pages/ResetPassword';
import Dashboard from './pages/Dashboard';
import AdminOnlyRoute from './auth/AdminOnlyRoute';
import Placeholder from './pages/Placeholder';
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

export default function App() {
    return (
        <Routes>
            <Route path="/login" element={<Login />} />
            <Route path="/forgot-password" element={<ForgotPassword />} />
            <Route path="/reset-password" element={<ResetPassword />} />
            <Route path="/verify-email" element={<VerifyEmail />} />

            <Route
                path="/register"
                element={
                    <RequireAuth>
                        <AdminOnlyRoute>
                            <Register />
                        </AdminOnlyRoute>
                    </RequireAuth>
                }
            />

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
                <Route path="invoices" element={<Placeholder title="Invoices" />} />
                <Route path="invoices/create" element={<Placeholder title="Create Invoice" />} />
                <Route path="invoices/:id" element={<Placeholder title="Invoice Detail" />} />
                <Route path="invoices/settings" element={<Placeholder title="Invoice Settings" />} />
                <Route path="inventory/products" element={<ProductsList />} />
                <Route path="inventory/products/create" element={<ProductForm />} />
                <Route path="inventory/products/:id" element={<ProductView />} />
                <Route path="inventory/products/:id/edit" element={<ProductForm />} />
                <Route path="inventory/transactions" element={<TransactionsList />} />
                <Route path="inventory/categories" element={<CategoriesList />} />
                <Route path="reports" element={<Placeholder title="Create Report" />} />
                <Route path="reports/register" element={<Placeholder title="Register Report" />} />
                <Route path="payments" element={<Placeholder title="Payments" />} />
                <Route path="settings" element={<Placeholder title="Settings" />} />
                <Route path="users" element={<Placeholder title="Users" />} />
                <Route path="logs" element={<Placeholder title="Activity Log" />} />
                <Route path="trash" element={<Placeholder title="Trash" />} />
                <Route path="profile" element={<Placeholder title="My Profile" />} />
                <Route path="*" element={<Navigate to="/dashboard" replace />} />
            </Route>
        </Routes>
    );
}