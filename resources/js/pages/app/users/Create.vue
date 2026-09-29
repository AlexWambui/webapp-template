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
    role_options: Record<string, string>;
    status_options: Record<string, string>;
}

const props = defineProps<Props>();

const roleOptions = computed(() => 
    Object.entries(props.role_options).map(([value, label]) => ({
        value: Number(value),
        label,
    }))
);

const statusOptions = computed(() =>
    Object.entries(props.status_options).map(([value, label]) => ({
        value: Number(value),
        label,
    }))
);
</script>

<template>
    <Head title="Create User" />

    <div class="form create-user">
        <FormHeader :backUrl="userRoutes.index().url" title="Create New user" />

        <Form :action="userRoutes.store.url()" method="post" v-slot="{ errors, processing }">
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
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="inputs-group">
                    <Label for="email" class="required">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>
            </div>

            <div class="inputs-group-wrapper">
                <div class="inputs-group">
                    <Label for="password" class="required">Password</Label>
                    <PasswordInput
                        id="password"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="inputs-group">
                    <Label for="password_confirmation" class="required">Confirm password</Label>
                    <PasswordInput
                        id="password_confirmation"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>
            </div>

            <div class="inputs-group-wrapper">
                <div class="inputs-group">
                    <Label for="role" class="required">User Role</Label>
                    <Select name="role">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select user role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem 
                                    v-for="option in roleOptions" 
                                    :key="option.value" 
                                    :value="option.value"
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
                    <Select name="status">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select account status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem 
                                    v-for="option in statusOptions" 
                                    :key="option.value" 
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>
            </div>

            <div class="submit-buttons">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" />
                    Create User
                </Button>

                <div>
                    <Link :href="userRoutes.index().url">
                        <Button type="button" variant="outline">
                            Cancel and return to users
                        </Button>
                    </Link>
                </div>
            </div>
        </Form>
    </div>
</template>