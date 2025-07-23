<x-layouts.user-app :title="__('Dashboard')">
    <div class="box-border sm:px-20 px-1">
        <div class="relative w-full h-[400px]  overflow-hidden">
            <img
            src="/images/profile/team2.jpg"
            alt="Description pertinente de l'image (par exemple, 'Photo de l'équipe du projet XYZ')" 
            class="object-cover w-full" 
            />
        </div>
        <div class="border-b-2 border-gray-300 sm:h-20 h-25 w-full flex sm:flex-row flex-col items-center justify-between sm:justify-between sm:px-20 px-1 box-border">
            <div>
                <p class="font-bold">Heneg Bayihe Joseph</p>
                <p class="text-sm flex text-orange-600">2,1K <Icon class="mr-4 text-black" path={mdiAccountOutline} size={0.8} /> 202K <Icon class="mr-4 text-black" path={mdiHeartOutline} size={0.8} /> 65 post</p>
            </div>
            <div>
                <button class="text-white bg-orange-600 rounded-lg text-sm p-2 border-1 border-orange-300">Send invitation</button>
                <button class="ml-6 bg-gray-200 rounded-lg text-sm p-2 border-1 border-gray-300">Block</button>
            </div>
        </div>
        <div class="flex sm:flex-row flex-col h-screen">
            <div class="sm:w-[40%] w-full pr-5 box-border relative">
                <div class="sticky top-0 z-10 p-4">
                    <h1 class=" text-xl font-bold mb-4">Informations</h1>
                    <h2 class=" text-lg font-bold mb-2">Generals Informations</h2>
                    <div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiEmailOutline} size={0.8} /> </h4>mhbbayihe12@gmail.com
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiMap} size={0.8} /> </h4>Douala, Cameroon
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4  text-black" path={mdiGithub} size={0.8} /> </h4>Heneg Bayihe
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-blue-600" path={mdiLinkedin} size={0.8} /> </h4>Heneg Bayihe
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-blue-600" path={mdiWeb} size={0.8} /> </h4><a class="text-blue-600" href="">NextApp.com</a>
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiGenderMale} size={0.8} /> </h4>24 years
                        </div>
                    </div>
                    <h2 class="mt-4 text-lg font-bold mb-2">School Informations</h2>
                    <div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiSchoolOutline} size={0.8} /> </h4>Lycée de la cité des palmiers
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiSchoolOutline} size={0.8} /> </h4>University of Douala Fac science
                        </div>
                        <div class="flex mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiSchoolOutline} size={0.8} /> </h4>ENSPD
                        </div>
                    </div>
                    <h2 class="mt-4 text-lg font-bold mb-2">Bibiographie</h2>
                    <div>
                        <div class="mb-2">
                            <h4><Icon class="mr-4 text-orange-600" path={mdiSchoolOutline} size={0.8} /> </h4>
                            <p class="text-sm">I am a student of ENSPD in am in 3 years Lorem, 
                                ipsum dolor sit amet consectetur adipisicing elit. 
                                Quis quo unde dolor debitis sunt necessitatibus rerum minus libero! 
                                Quaerat inventore maxime vero expedita velit accusamus consectetur 
                                laboriosam ratione neque minima?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sm:w-[60%] w-full">
                <h1 class=" text-xl font-bold mb-4">Post</h1>
                <div class=" h-screen overflow-y-auto no-scrollbar">
                    <div class="w-full border-1 border-gray-400 rounded-lg mb-10">
                        <div class="flex justify-between items-center py-2 px-1">
                            <div class="flex items-center">
                            <div >
                                <Image
                                    class="border rounded-full mr-4"
                                    src="/profil/team2.jpg"
                                    alt="Vercel logomark"
                                    width={45}
                                    height={45}
                                />
                            </div>
                            <div>
                                <p class="text-sm font-bold">heneg_bayihe</p>
                                <p class="text-xs font-thin text-gray-400">Heneg Bayihe</p>
                            </div>
                            </div>
                            <div class="flex">
                            <p class="text-sm mr-4">2 days</p> <Icon class="mr-2" path={mdiDotsHorizontal} size={1} />
                            </div>
                        </div>
                        <div class="relative w-full h-[300px]  overflow-hidden">
                            <Image
                            src="/profil/team2.jpg"
                            alt="Description pertinente de l'image (par exemple, 'Photo de l'équipe du projet XYZ')" 
                            fill
                            class="object-cover" 
                            />
                        </div>
                        <div class="px-1 text-justify">
                            <p class="text-sm">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Cumque officiis
                                eos expedita ad magnam! Voluptatem animi dolores, totam tempore eos nostrum, ...
                            </p>
                        </div>
                        <div class="py-2 px-1  pb-4 flex items-center">
                            <p class="text-sm text-orange-600 mr-1">200</p><Icon class="mr-4" path={mdiHeartOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">25</p><Icon class="mr-4" path={mdiBookmarkOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">1</p><Icon class="mr-4" path={mdiShareOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">19K</p><Icon class="" path={mdiCommentOutline} size={1} />
                        </div>
                        <div class="px-1 pb-4">
                            <div class="bg-gray-100 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <p class="text-sm">Comments</p>
                            </div>
                            <form action="">
                                <textarea class="border-1 border-gray-300 rounded-lg w-full h-[40px] resize-none bg-white focus:outline-none focus:border-gray-500 focus:ring-0 p-1" name="" id=""></textarea>
                                <div class="flex justify-end"><button class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white">Send <Icon class="ml-2" path={mdiSendOutline} size={0.8} /></button></div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="w-full border-1 border-gray-400 rounded-lg mb-10">
                        <div class="flex justify-between items-center py-2 px-1">
                            <div class="flex items-center">
                            <div >
                                <Image
                                    class="border rounded-full mr-4"
                                    src="/profil/team2.jpg"
                                    alt="Vercel logomark"
                                    width={45}
                                    height={45}
                                />
                            </div>
                            <div>
                                <p class="text-sm font-bold">heneg_bayihe</p>
                                <p class="text-xs font-thin text-gray-400">Heneg Bayihe</p>
                            </div>
                            </div>
                            <div class="flex">
                            <p class="text-sm mr-4">2 days</p> <Icon class="mr-2" path={mdiDotsHorizontal} size={1} />
                            </div>
                        </div>
                        <div class="px-1 text-justify">
                            <p class="text-sm">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Cumque officiis
                                eos expedita ad magnam! Voluptatem animi dolores, totam tempore eos nostrum, ...
                            </p>
                        </div>
                        <div class="py-2 px-1  pb-4 flex items-center">
                            <p class="text-sm text-orange-600 mr-1">200</p><Icon class="mr-4" path={mdiHeartOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">25</p><Icon class="mr-4" path={mdiBookmarkOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">1</p><Icon class="mr-4" path={mdiShareOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">19K</p><Icon class="" path={mdiCommentOutline} size={1} />
                        </div>
                        <div class="px-1 pb-4">
                            <div class="bg-gray-100 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <p class="text-sm">Comments</p>
                            </div>
                            <form action="">
                                <textarea class="border-1 border-gray-300 rounded-lg w-full h-[40px] resize-none bg-white focus:outline-none focus:border-gray-500 focus:ring-0 p-1" name="" id=""></textarea>
                                <div class="flex justify-end"><button class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white">Send <Icon class="ml-2" path={mdiSendOutline} size={0.8} /></button></div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <div class="w-full border-1 border-gray-400 rounded-lg mb-10">
                        <div class="flex justify-between items-center py-2 px-1">
                            <div class="flex items-center">
                            <div >
                                <Image
                                    class="border rounded-full mr-4"
                                    src="/profil/team2.jpg"
                                    alt="Vercel logomark"
                                    width={45}
                                    height={45}
                                />
                            </div>
                            <div>
                                <p class="text-sm font-bold">heneg_bayihe</p>
                                <p class="text-xs font-thin text-gray-400">Heneg Bayihe</p>
                            </div>
                            </div>
                            <div class="flex">
                            <p class="text-sm mr-4">2 days</p> <Icon class="mr-2" path={mdiDotsHorizontal} size={1} />
                            </div>
                        </div>
                        <div class="relative w-full h-[300px]  overflow-hidden">
                            <Image
                            src="/profil/team2.jpg"
                            alt="Description pertinente de l'image (par exemple, 'Photo de l'équipe du projet XYZ')" 
                            fill
                            class="object-cover" 
                            />
                        </div>
                        <div class="py-2 px-1  pb-4 flex items-center">
                            <p class="text-sm text-orange-600 mr-1">200</p><Icon class="mr-4" path={mdiHeartOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">25</p><Icon class="mr-4" path={mdiBookmarkOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">1</p><Icon class="mr-4" path={mdiShareOutline} size={1} />
                            <p class="text-sm text-orange-600 mr-1">19K</p><Icon class="" path={mdiCommentOutline} size={1} />
                        </div>
                        <div class="px-1 pb-4">
                            <div class="bg-gray-100 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <p class="text-sm">Comments</p>
                            </div>
                            <form action="">
                                <textarea class="border-1 border-gray-300 rounded-lg w-full h-[40px] resize-none bg-white focus:outline-none focus:border-gray-500 focus:ring-0 p-1" name="" id=""></textarea>
                                <div class="flex justify-end"><button class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white">Send <Icon class="ml-2" path={mdiSendOutline} size={0.8} /></button></div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.user-app>
