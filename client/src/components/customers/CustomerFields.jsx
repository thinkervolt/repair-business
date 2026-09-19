import React from 'react';
import { useI18n } from '../../i18n/I18nContext';
import Input from '../ui/Input';
import Field from '../ui/Field';

export default function CustomerFields({ value, onChange, errors = {} }) {
    const { t } = useI18n();
    const set = (key) => (e) => onChange({ ...value, [key]: e.target.value });

    return (
        <div className="grid gap-4 sm:grid-cols-2">
            <Field label={t('customers.form.first_name')} error={errors.first_name}>
                <Input
                    type="text"
                    value={value.first_name || ''}
                    onChange={set('first_name')}
                    invalid={!!errors.first_name}
                    required
                    autoFocus
                />
            </Field>
            <Field label={t('customers.form.last_name')} error={errors.last_name}>
                <Input
                    type="text"
                    value={value.last_name || ''}
                    onChange={set('last_name')}
                    invalid={!!errors.last_name}
                />
            </Field>
            <Field label={t('customers.form.phone')} error={errors.phone}>
                <Input
                    type="tel"
                    value={value.phone || ''}
                    onChange={set('phone')}
                    placeholder="5550001234"
                    invalid={!!errors.phone}
                    required
                />
            </Field>
            <Field label={t('customers.form.email')} error={errors.email}>
                <Input
                    type="email"
                    value={value.email || ''}
                    onChange={set('email')}
                    invalid={!!errors.email}
                />
            </Field>
            <Field label={t('customers.form.company')} error={errors.company}>
                <Input
                    type="text"
                    value={value.company || ''}
                    onChange={set('company')}
                    invalid={!!errors.company}
                />
            </Field>
            <Field label={t('customers.form.address')} error={errors.address}>
                <Input
                    type="text"
                    value={value.address || ''}
                    onChange={set('address')}
                    invalid={!!errors.address}
                />
            </Field>
            <Field label={t('customers.form.city')} error={errors.city}>
                <Input
                    type="text"
                    value={value.city || ''}
                    onChange={set('city')}
                    invalid={!!errors.city}
                />
            </Field>
            <Field label={t('customers.form.state')} error={errors.state} hint={t('customers.form.state_hint')}>
                <Input
                    type="text"
                    value={value.state || ''}
                    onChange={set('state')}
                    maxLength={2}
                    invalid={!!errors.state}
                />
            </Field>
            <Field label={t('customers.form.zip')} error={errors.zip}>
                <Input
                    type="text"
                    value={value.zip || ''}
                    onChange={set('zip')}
                    maxLength={5}
                    invalid={!!errors.zip}
                />
            </Field>
        </div>
    );
}