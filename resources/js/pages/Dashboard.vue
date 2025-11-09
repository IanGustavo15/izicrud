<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

import { BarChart } from "@/components/ui/chart-bar";

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'

import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

import { Bell, Check } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import {
  CardDescription,
  CardFooter,
} from "@/components/ui/card"
import { Switch } from "../components/ui/switch"

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const dataBar = [
  { name: "Jan", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "Feb", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "Mar", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "Apr", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "May", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "Jun", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
  { name: "Jul", total: Math.floor(Math.random() * 2000) + 500, predicted: Math.floor(Math.random() * 2000) + 500 },
]

const notifications = [
  {
    title: "Your call has been confirmed.",
    description: "1 hour ago",
  },
  {
    title: "You have a new message!",
    description: "1 hour ago",
  },
  {
    title: "Your subscription is expiring soon!",
    description: "2 hours ago",
  },
]

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
         <div class="md:hidden">
            <VPImage
            alt="Dashboard"
            width="1280"
            height="1214" class="block" :image="{
                dark: '/examples/dashboard-dark.png',
                light: '/examples/dashboard-light.png',
            }"
            />
        </div>

        <div class="hidden flex-col md:flex">
            <div class="flex-1 space-y-4 p-8 pt-6">
            <div class="flex items-center justify-between space-y-2">
                <h2 class="text-3xl font-bold tracking-tight">
                Dashboard
                </h2>
            </div>
            <Tabs default-value="overview" class="space-y-4">
                <TabsList>
                <TabsTrigger value="overview">
                    Visão Geral
                </TabsTrigger>
                <TabsTrigger value="analytics">
                    Rendimento
                </TabsTrigger>
                <TabsTrigger value="reports">
                    Rank Geral
                </TabsTrigger>
                <TabsTrigger value="notifications">
                    Notificações
                </TabsTrigger>
                </TabsList>
                <TabsContent value="overview" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Total Revenue
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        $45,231.89
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +20.1% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Subscriptions
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +2350
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +180.1% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Sales
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <rect width="20" height="14" x="2" y="5" rx="2" />
                        <path d="M2 10h20" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +12,234
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +19% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Active Now
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +573
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +201 since last hour
                        </p>
                    </CardContent>
                    </Card>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Overview</CardTitle>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <BarChart
                            index="name"
                            :data="dataBar"
                            :categories="['total', 'predicted']"
                            :y-formatter="(tick, i) => {
                            return typeof tick === 'number'
                                ? `$ ${new Intl.NumberFormat('us').format(tick).toString()}`
                                : ''
                            }"
                            :type="'stacked'"
                        />
                    </CardContent>
                    </Card>

                      <Card class="col-span-3">
                        <CardHeader>
                        <CardTitle>Notifications</CardTitle>
                        <CardDescription>You have 3 unread messages.</CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-4">
                        <div class=" flex items-center space-x-4 rounded-md border p-4">
                            <Bell />
                            <div class="flex-1 space-y-1">
                            <p class="text-sm font-medium leading-none">
                                Push Notifications
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Send notifications to device.
                            </p>
                            </div>
                            <Switch />
                        </div>
                        <div>
                            <div
                            v-for="(notification, index) in notifications" :key="index"
                            class="mb-4 grid grid-cols-[25px_minmax(0,1fr)] items-start pb-4 last:mb-0 last:pb-0"
                            >
                            <span class="flex h-2 w-2 translate-y-1 rounded-full bg-sky-500" />
                            <div class="space-y-1">
                                <p class="text-sm font-medium leading-none">
                                {{ notification.title }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                {{ notification.description }}
                                </p>
                            </div>
                            </div>
                        </div>
                        </CardContent>
                        <CardFooter>
                        <Button class="w-full">
                            <Check class="mr-2 h-4 w-4" /> Mark all as read
                        </Button>
                        </CardFooter>
                    </Card>

                </div>
                </TabsContent>
                <TabsContent value="analytics" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Media de Rendimento
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        $45,231.89
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +20.1% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Subscriptions
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +2350
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +180.1% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Sales
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <rect width="20" height="14" x="2" y="5" rx="2" />
                        <path d="M2 10h20" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +12,234
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +19% from last month
                        </p>
                    </CardContent>
                    </Card>
                    <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                        Active Now
                        </CardTitle>
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        class="h-4 w-4 text-muted-foreground"
                        >
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                        +573
                        </div>
                        <p class="text-xs text-muted-foreground">
                        +201 since last hour
                        </p>
                    </CardContent>
                    </Card>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Overview</CardTitle>
                    </CardHeader>
                    <CardContent class="pl-2">
                        <Overview />
                    </CardContent>
                    </Card>
                    <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Recent Sales</CardTitle>
                        <CardDescription>
                        You made 265 sales this month.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <RecentSales />
                    </CardContent>
                    </Card>
                </div>
                </TabsContent>
            </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
