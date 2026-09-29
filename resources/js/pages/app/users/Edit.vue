<script setup lang="ts">
import {computed} from 'vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import FormHeader from '@/components/custom/FormHeader.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import userRoutes from '@/routes/users';

interface Props {
    user: {
        data: {
            id: number;
            uuid: string;
            name: string;
            email: string;
            role: number;
            status: number;
            is_active: boolean;
            role_label: string;
        };
    };
    role_options: Record<string, string>;
    status_options: Record<string, string>;
}

const statusOptions = computed(() =>
    Object.entries(props.status_options).map(([value, label]) => ({
        value: Number(value),
        label,
    }))
);

const props = defineProps<Props>();
const userData = props.user.data;

const roleOptions = computed(() => {
    const entries = Object.entries(props.role_options).map(([value, label]) => ({
        value: Number(value),
        label,
    }));

    // Ensure the user's current role is present even if not normally assignable
    // (shouldn't happen due to policy, but defensive)
    if (!entries.some(o => o.value === userData.role)) {
        entries.push({ value: userData.role, label: userData.role_label });
    }

    return entries;
});
</script>

<template>
    <Head title="Edit User" />

    <div class="form edit-user">
        <FormHeader :backUrl="userRoutes.index().url" title="Edit user" />

        <Form :action="userRoutes.update(userData.uuid).url" method="put" v-slot="{ errors, processing }">
            <div class="section-title">Basic Information</div>
            
            <div class="inputs-group-wrapper">
                <div class="inputs-group">
                    <Label for="name" class="required">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        autofocus
                        autocomplete="name"
                        name="name"
                        :default-value="userData.name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="inputs-group">
                    <Label for="email" class="required">Email Address</Label>
                    <Input
                        id="email"
                        type="email"
                        autocomplete="email"
                        name="email"
                        :default-value="userData.email"
                        placeholder="Email address"
                    />
                    <InputError :message="errors.email" />
                </div>
            </div>

            <div class="inputs-group-wrapper">
                <div class="inputs-group">
                    <Label for="role" class="required">User Role</Label>
                    <Select name="role" :default-value="String(userData.role)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select user role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem 
                                    v-for="option in roleOptions" 
                                    :key="option.value" 
                                    :value="String(option.value)"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.role" />
                </div>

                <div class="inputs-group">
                    <Label for="status" class="required">Account Status</Label>
                    <Select name="status" :default-value="String(userData.status)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select account status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem 
                                    v-for="option in statusOptions" 
                                    :key="option.value" 
                                    :value="String(option.value)"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>
            </div>

            <div class="inputs-group-wrapper">
                <div class="inputs-group">
                    <Label for="password">Password (leave blank to keep current)</Label>
                    <PasswordInput
                        id="password"
                        autocomplete="new-password"
                        name="password"
                        placeholder="New password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="inputs-group">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <PasswordInput
                        id="password_confirmation"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>
            </div>

            <div class="submit-buttons">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" />
                    Update User
                </Button>

                <Link :href="userRoutes.index().url">
                    <Button type="button" variant="outline">
                        Cancel and return to users
                    </Button>
                </Link>
            </div>
        </Form>
    </div>
</template>
